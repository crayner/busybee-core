<?php

namespace Busybee\CurriculumBundle\Model;

/**
 * Subject
 */
class Subject
{
	public function getNameVersion()
	{
		return $this->getName().' '.$this->getVersion();
	}
}
