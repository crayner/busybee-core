<?php

namespace Busybee\People\StudentBundle\Model;

abstract class StudentGradeModel
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

	/**
	 * @return string
	 */
	public function getStudentName(): string
	{
		if (!is_null($this->getStudent()))
			return $this->getStudent()->formatName();

		return '';
	}

	/**
	 * @return int
	 */
	public function getStudentId(): int
	{
		if (!is_null($this->getStudent()))
			return $this->getStudent()->getId();

		return 0;
	}
}