<?php

namespace Busybee\Management\GradeBundle\Model;

class StudentGradeModel
{
	/**
	 * @return string
	 */
	public function getGradeYear()
	{
		if (!empty($this->getGrade()))
			return $this->getGrade()->getGradeYear();

		return null;
	}

	/**
	 * @return bool
	 */
	public function canDelete()
	{
		return true;
	}

	/**
	 * @return Year|null
	 */
	public function getYear()
	{
		if (!empty($this->getGrade()))
			return $this->getGrade()->getYear();

		return null;
	}
}