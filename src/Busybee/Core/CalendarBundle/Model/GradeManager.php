<?php

namespace Busybee\Core\CalendarBundle\Model;


use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\People\StaffBundle\Entity\Staff;
use Doctrine\Common\Persistence\ObjectManager;
use Busybee\Core\CalendarBundle\Entity\Year;

class GradeManager
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
	 * GradeManager constructor.
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
	public function getYearGrades()
	{
		return $this->om->getRepository(Grade::class)->createQueryBuilder('g')
			->leftJoin('g.year', 'y')
			->where('y.id = :year_id')
			->setParameter('year_id', $this->year->getId())
			->orderBy('g.sequence', 'ASC')
			->getQuery()
			->getResult();
	}

	/**
	 * Delete Student Grade
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function deleteStudentGrade($id)
	{
		$status            = [];
		$status['message'] = 'student.grade.notfound';
		$status['status']  = 'warning';
		if (intval($id < 1))
			return $status;

		$entity = $this->om->getRepository(StudentGrade::class)->find($id);

		if (is_null($entity))
			return $status;

		if (!$entity->canDelete())
		{
			$status            = [];
			$status['message'] = 'student.grade.remove.blocked';
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
	public function getTutorNames(Grade $entity): string
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
	public function getSpaceName(Grade $entity): string
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
	 * @return Grade|null
	 */
	public function getEntity(int $id = null)
	{
		return $this->om->getRepository(Grade::class)->find($id);
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
		if (class_exists('Busybee\People\StudentBundle\Model\StudentModel'))
		{
			$metaData = $this->getOm()->getClassMetadata('\Busybee\People\StudentBundle\Entity\StudentGrade');
			$schema   = $this->getOm()->getConnection()->getSchemaManager();

			return $schema->tablesExist([$metaData->table['name']]);
		}

		return false;
	}
}