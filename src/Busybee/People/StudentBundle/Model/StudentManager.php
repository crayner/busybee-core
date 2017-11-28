<?php

namespace Busybee\People\StudentBundle\Model;

use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\SecurityBundle\Security\UserProvider;
use Busybee\Core\SystemBundle\Model\MessageManager;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\People\StudentBundle\Entity\Student;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\People\StudentBundle\Entity\StudentGrade;
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
	private $grade;

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

		$grade = null;
		foreach ($student->getGrades() as $sg)
		{
			$grade = $sg->getGrade();
			if ($grade->getYear()->getId() == $year->getId())
				break;
			$grade = null;
		}

		if (is_null($grade))
			return $student->formatName($options) . ' (No Grade in ' . $year->getName() . '.)';

		return $student->formatName($options) . ' (' . $grade->getName() . ')';
	}

	/**
	 * @param int $id
	 */
	public function initiateGrade(int $id, $bypass = true)
	{
		if ($this->initiated && $bypass) return;

		$this->grade = $this->getOm()->getRepository(Grade::class)->find($id);

		$this->students = $this->getPossibleStudents($bypass);

		$this->getOm()->refresh($this->grade);

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
	 * @return Grade
	 */
	public function getGrade(): Grade
	{
		return $this->grade;
	}

	/**
	 * @param Request $request
	 * @param Form    $form
	 */
	public function handleRequest(Request $request, Form $form)
	{
		$data = $request->get('add_students_to_grade');

		if (is_null($data)) return;

		foreach ($data['students'] as $stuId)
		{
			if (!isset($this->current[$stuId]))
			{
				$sg = new StudentGrade();
				$sg->setGrade($this->grade);
				$sg->setStatus($data['defaultStatus']);
				$sg->setStudent($this->getOm()->getRepository(Student::class)->find($stuId));
				$this->grade->addStudent($sg);
			}
		}

		$this->getOm()->persist($this->grade);
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
			->leftJoin('s.grades', 'sg')
			->where("sg.grade = :grade_id")
			->setParameter('grade_id', $this->grade->getId())
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

		$yearGrades = $this->getOm()->getRepository(Grade::class)->findByYear($this->grade->getYear());

		$grades = [];

		foreach ($yearGrades as $w)
			if ($w->getId() !== $this->grade->getId())
				$grades[] = $w->getId();

		$exclude = $this->getOm()->getRepository(Student::class)->createQueryBuilder('s')
			->leftJoin('s.grades', 'sg')
			->leftJoin('sg.grade', 'g')
			->orderBy('s.surname', 'ASC')
			->addOrderBy('s.firstName', 'ASC')
			->where('g.id IN (:grades)')
			->setParameter('grades', $grades, Connection::PARAM_INT_ARRAY)
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
	public function addStudentToGrade($student, $status)
	{
		$student = $this->getOm()->getRepository(Student::class)->find(str_replace('student_', '', $student));

		$sg = new StudentGrade();
		$sg->setStudent($student);
		$sg->setStatus($status);
		$sg->setGrade($this->grade);

		try
		{
			$this->getOm()->persist($this->grade);
			$this->getOm()->flush();
		}
		catch (\Exception $e)
		{
			$this->addMessage('danger', 'grade.student.added.failed', ['%message%' => $e->getMessage()]);

			return;
		}

		$this->addMessage('success', 'grade.student.added.success', ['%name%' => $student->formatName()]);
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
	public function removeStudentFromGrade($student)
	{
		$sg = $this->getOm()->getRepository(StudentGrade::class)->findOneBy(['student' => str_replace("student_", '', $student), 'grade' => $this->grade->getId()]);

		$this->grade->removeStudent($sg);

		try
		{
			$this->getOm()->persist($this->grade);
			$this->getOm()->remove($sg);
			$this->getOm()->flush();
		}
		catch (\Exception $e)
		{
			$this->addMessage('danger', 'grade.student.remove.failed', ['%message%' => $e->getMessage()]);

			return;
		}

		$this->addMessage('warning', 'grade.student.remove.success', ['%name%' => $sg->getStudent()->formatName()]);
	}
}