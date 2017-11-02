<?php

namespace Busybee\AVETMISS\AVETMISSBundle\Model;

use Busybee\Program\CurriculumBundle\Entity\Subject as SubjectCore;

/**
 * Subject
 */
class Subject
{
	public function __construct()
	{
		$this->setSubject(new SubjectCore());
	}

	public function getNameVersion()
	{
		if ($this->getSubject() instanceof SubjectCore)
			return $this->getSubject()->getNameVersion();

		return '';
	}
}