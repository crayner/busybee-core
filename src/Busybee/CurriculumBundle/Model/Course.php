<?php

namespace Busybee\CurriculumBundle\Model;

/**
 * Course
 */
class Course
{
	public function getNameVersion()
	{
		return $this->getName().' '.$this->getVersion();
	}
}
