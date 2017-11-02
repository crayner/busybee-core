<?php

namespace Busybee\Program\GradeBundle\Model;

use Busybee\Program\GradeBundle\Entity\StudentGrade;
use Doctrine\Common\Persistence\ObjectManager;

class GradeManager
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * GradeManager constructor.
	 *
	 * @param ObjectManager $om
	 */
	public function __construct(ObjectManager $om)
	{
		$this->om = $om;
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
}