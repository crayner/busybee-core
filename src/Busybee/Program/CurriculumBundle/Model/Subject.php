<?php

namespace Busybee\Program\CurriculumBundle\Model;

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
