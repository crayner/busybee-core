<?php
namespace Busybee\Core\CalendarBundle\Model;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\People\StudentBundle\Entity\StudentCalendarGroup;
use Doctrine\Common\Persistence\ObjectManager;
use Busybee\Core\CalendarBundle\Entity\Year;

class CalendarGroupManager
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * @var Year
	 */
	private $year;

	/**
	 * CalendarGroupManager constructor.
	 *
	 * @param ObjectManager $om
	 * @param Year          $year
	 */
	public function __construct(ObjectManager $om, Year $year)
	{
		$this->om   = $om;
		$this->year = $year;
	}

	/**
	 * @return mixed
	 */
	public function getYearCalendarGroups()
	{
		return $this->om->getRepository(CalendarGroup::class)->createQueryBuilder('g')
			->leftJoin('g.year', 'y')
			->where('y.id = :year_id')
			->setParameter('year_id', $this->year->getId())
			->orderBy('g.sequence', 'ASC')
			->getQuery()
			->getResult();
	}

	/**
	 * Delete Student CalendarGroup
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function deleteStudentCalendarGroup($id)
	{
		$status            = [];
		$status['message'] = 'student.calendar.group.notfound';
		$status['status']  = 'warning';
		if (intval($id < 1))
			return $status;

		$entity = $this->om->getRepository(StudentCalendarGroup::class)->find($id);

		if (is_null($entity))
			return $status;

		if (!$entity->canDelete())
		{
			$status            = [];
			$status['message'] = 'student.calendar.group.remove.blocked';
			$status['status']  = 'warning';

			return $status;
		}
		try
		{
			$this->om->remove($entity);
			$this->om->flush();
		}
		catch (\Exception $e)
		{
			$status            = [];
			$status['message'] = 'student.grade.remove.fail';
			$status['status']  = 'error';

			return $status;
		}
		$status            = [];
		$status['message'] = 'student.grade.remove.success';
		$status['status']  = 'success';

		return $status;
	}

	/**
	 * @return string
	 */
	public function getTutorNames(CalendarGroup $entity): string
	{
		if (empty($entity))
			return '';

		$names = '';

		if (!empty($entity->getTutor1() && $entity->getTutor1() instanceof Staff))
			$names .= $entity->getTutor1()->formatName();

		if (!empty($entity->getTutor2() && $entity->getTutor2() instanceof Staff))
			$names .= "<br />" . $entity->getTutor2()->formatName();

		if (!empty($entity->getTutor3() && $entity->getTutor3() instanceof Staff))
			$names .= "<br />" . $entity->getTutor3()->formatName();

		return $names;
	}

	/**
	 * @return string
	 */
	public function getSpaceName(CalendarGroup $entity): string
	{
		if (empty($entity))
			return '';

		if ($entity->getSpace() instanceof Space)
			return $entity->getSpace()->getName();

		return '';
	}

	/**
	 * @param int|null $id
	 *
	 * @return CalendarGroup|null
	 */
	public function getEntity(int $id = null)
	{
		return $this->getOm()->getRepository(CalendarGroup::class)->find($id);
	}

	/**
	 * @return ObjectManager
	 */
	public function getOm(): ObjectManager
	{
		return $this->om;
	}

	/**
	 * @return bool
	 */
	public function isStudentInstalled(): bool
	{
		if (class_exists('Busybee\People\StudentBundle\Entity\StudentCalendarGroup'))
		{
			$metaData = $this->getOm()->getClassMetadata('\Busybee\People\StudentBundle\Entity\StudentCalendarGroup');
			$schema   = $this->getOm()->getConnection()->getSchemaManager();

			return $schema->tablesExist([$metaData->table['name']]);
		}

		return false;
	}
}