<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model;

/**
 * Course
 */
class Course
{
	public function __construct()
	{
		$this->setCourse(new \Busybee\CurriculumBundle\Entity\Course());
	}

	public function getNameVersion()
	{
		if (is_null($this->getCourse()))
			return null;

		return $this->getCourse()->getNameVersion();
	}
}