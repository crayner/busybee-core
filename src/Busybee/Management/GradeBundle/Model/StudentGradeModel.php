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

		return '';
	}

	public function canDelete()
	{
		return true;
	}
}