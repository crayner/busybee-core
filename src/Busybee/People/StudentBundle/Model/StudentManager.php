<?php

namespace Busybee\People\StudentBundle\Model;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\SecurityBundle\Security\UserProvider;
use Busybee\Core\SystemBundle\Model\MessageManager;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\People\StudentBundle\Entity\Student;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\People\StudentBundle\Entity\StudentCalendarGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class StudentManager extends PersonManager
{
	/**
	 * @var Grade
	 */
	private $calendarGroup;

	/**
	 * @var ArrayCollection
	 */
	private $students;

	/**
	 * @var ArrayCollection
	 */
	private $current;

	/**
	 * @var ArrayCollection
	 */
	private $excluded;

	/**
	 * @var bool
	 */
	private $initiated = false;

	/**
	 * @var MessageManager
	 */
	private $messages;

	/**
	 * StudentManager constructor.
	 *
	 * @param ObjectManager  $om
	 * @param SettingManager $sm
	 * @param UserProvider   $up
	 */
	public function __construct(ObjectManager $om, SettingManager $sm, UserProvider $up)
	{
		parent::__construct($om, $sm, $up);

		$this->students = new ArrayCollection();
		$this->messages = new MessageManager('BusybeeStudentBundle');
	}

	/**
	 * @param       $student_id
	 * @param Year  $year
	 * @param array $options
	 *
	 * @return string
	 */
	public function getStudentNameWithGrade($student_id, Year $year, $options = [])
	{
		$student = $this->getOm()->getRepository(Student::class)->find(intval($student_id));

		$calendarGroup = null;
		foreach ($student->getGrades() as $sg)
		{
			$calendarGroup = $sg->getGrade();
			if ($calendarGroup->getYear()->getId() == $year->getId())
				break;
			$calendarGroup = null;
		}

		if (is_null($calendarGroup))
			return $student->formatName($options) . ' (No Grade in ' . $year->getName() . '.)';

		return $student->formatName($options) . ' (' . $calendarGroup->getName() . ')';
	}

	/**
	 * @param int $id
	 */
	public function initiateCalendarGroup(int $id, $bypass = true)
	{
		if ($this->initiated && $bypass) return;

		$this->calendarGroup = $this->getOm()->getRepository(CalendarGroup::class)->find($id);

		$this->students = $this->getPossibleStudents($bypass);

		$this->getOm()->refresh($this->calendarGroup);

		$this->initiated = true;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getStudents(): ArrayCollection
	{
		return $this->students;
	}

	/**
	 * @param ArrayCollection $students
	 *
	 * @return StudentManager
	 */
	public function setStudents(ArrayCollection $students): StudentManager
	{
		$this->students = $students;

		return $this;
	}

	/**
	 * @return CalendarGroup
	 */
	public function getCalendarGroup(): CalendarGroup
	{
		return $this->calendarGroup;
	}

	/**
	 * @param Request $request
	 * @param Form    $form
	 */
	public function handleRequest(Request $request, Form $form)
	{
		$data = $request->get('add_students_to_calendarGroup');

		if (is_null($data)) return;

		foreach ($data['students'] as $stuId)
		{
			if (!isset($this->current[$stuId]))
			{
				$sg = new StudentCalendarGroup();
				$sg->setCalendarGroup($this->calendarGroup);
				$sg->setStatus($data['defaultStatus']);
				$sg->setStudent($this->getOm()->getRepository(Student::class)->find($stuId));
				$this->calendarGroup->addStudent($sg);
			}
		}

		$this->getOm()->persist($this->calendarGroup);
		$this->getOm()->flush();

		$form->handleRequest($request);
	}

	/**
	 * @return ArrayCollection
	 */
	public function getPossibleStudents($bypass = true)
	{
		if ($this->initiated && $bypass) return $this->students;

		$this->current = $this->getCurrentStudents($bypass);

		$status = $this->getSm()->get('student.enrolment.status');

		$x = [];
		foreach ($status['Enrolled'] as $q => $w)
			$x[] = $q;


		$students = $this->getOm()->getRepository(Student::class)->createQueryBuilder('s')
			->orderBy('s.surname', 'ASC')
			->addOrderBy('s.firstName', 'ASC')
			->where("s.status IN (:enrolled)")
			->setParameter('enrolled', $x, Connection::PARAM_STR_ARRAY)
			->getQuery()
			->getResult();


		$this->students = new ArrayCollection();

		$this->excluded = $this->getExcludedStudents($bypass);

		foreach ($students as $student)
			if (!$this->excluded->contains($student) && !$this->current->contains($student))
				$this->students->add($student);

		return $this->students;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getCurrentStudents($bypass = true)
	{
		if ($this->initiated && $bypass) return $this->current;

		$current = $this->getOm()->getRepository(Student::class)->createQueryBuilder('s')
			->leftJoin('s.calendarGroups', 'sg')
			->where("sg.calendarGroup = :calendar_group_id")
			->setParameter('calendar_group_id', $this->calendarGroup->getId())
			->orderBy('s.surname')
			->addOrderBy('s.firstName')
			->getQuery()
			->getResult();

		$this->current = new ArrayCollection($current);

		return $this->current;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getExcludedStudents($bypass = true)
	{
		if ($this->initiated && $bypass) return $this->excluded;

		$yearGrades = $this->getOm()->getRepository(CalendarGroup::class)->findByYear($this->calendarGroup->getYear());

		$calendarGroups = [];

		foreach ($yearGrades as $w)
			if ($w->getId() !== $this->calendarGroup->getId())
				$calendarGroups[] = $w->getId();

		$exclude = $this->getOm()->getRepository(Student::class)->createQueryBuilder('s')
			->leftJoin('s.calendarGroups', 'sg')
			->leftJoin('sg.calendarGroup', 'g')
			->orderBy('s.surname', 'ASC')
			->addOrderBy('s.firstName', 'ASC')
			->where('g.id IN (:calendarGroups)')
			->setParameter('calendarGroups', $calendarGroups, Connection::PARAM_INT_ARRAY)
			->getQuery()
			->getResult();

		$this->excluded = new ArrayCollection($exclude);

		return $this->excluded;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getCurrent(): ArrayCollection
	{
		return $this->current;
	}

	/**
	 * @param $student
	 * @param $status
	 */
	public function addStudentToCalendarGroup($student, $status)
	{
		$student = $this->getOm()->getRepository(Student::class)->find(str_replace('student_', '', $student));

		$sg = new StudentCalendarGroup();
		$sg->setStudent($student);
		$sg->setStatus($status);
		$sg->setCalendarGroup($this->calendarGroup);

		try
		{
			$this->getOm()->persist($this->calendarGroup);
			$this->getOm()->flush();
		}
		catch (\Exception $e)
		{
			$this->addMessage('danger', 'calendar.group.student.added.failed', ['%message%' => $e->getMessage()]);

			return;
		}

		$this->addMessage('success', 'calendar.group.student.added.success', ['%name%' => $student->formatName()]);
	}

	/**
	 * @return MessageManager
	 */
	public function getMessages(): MessageManager
	{
		return $this->messages;
	}

	public function addMessage(string $level, string $message, array $options = [], string $domain = null)
	{
		$this->messages->addMessage($level, $message, $options, $domain);
	}

	/**
	 * @param $student
	 * @param $status
	 */
	public function removeStudentFromCalendarGroup($student)
	{
		$sg = $this->getOm()->getRepository(StudentCalendarGroup::class)->findOneBy(['student' => str_replace("student_", '', $student), 'calendarGroup' => $this->calendarGroup->getId()]);

		$this->calendarGroup->removeStudent($sg);

		try
		{
			$this->getOm()->persist($this->calendarGroup);
			$this->getOm()->remove($sg);
			$this->getOm()->flush();
		}
		catch (\Exception $e)
		{
			$this->addMessage('danger', 'calendar.group.student.remove.failed', ['%message%' => $e->getMessage()]);

			return;
		}

		$this->addMessage('warning', 'calendar.group.student.remove.success', ['%name%' => $sg->getStudent()->formatName()]);
	}

	/**
	 * @return string
	 */
	public function getLinks()
	{
		$links  = '<p>';
		$anchor = '';
		foreach ($this->getPossibleStudents() as $student)
			if (substr($student->getSurname(), 0, 1) !== $anchor)
			{
				$anchor = substr($student->getSurname(), 0, 1);
				$links  .= '<a class="btn btn-primary" style="width: 45px;" href="#' . $anchor . '">[' . $anchor . ']</a>';
			}

		return $links . '</p>';
	}
}