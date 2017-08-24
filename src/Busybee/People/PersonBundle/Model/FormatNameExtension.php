<?php

namespace Busybee\People\PersonBundle\Model;

use Busybee\People\FamilyBundle\Entity\CareGiver;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\Common\CommonException;

trait FormatNameExtension
{
	/**
	 * @return string
	 * @throws CommonException
	 */
	public function getFullName($options = array())
	{
		return $this->formatName($options);
	}

	/**
	 * @return string
	 * @throws CommonException
	 */
	public function formatName($options = array())
	{
		if ($this instanceof CareGiver)
		{
			if (empty($options))
			{
				$options['preferredOnly'] = true;
			}
			$person = $this->getPerson();
			if ($person instanceof Person)
				return $person->formatName($options);
		}

		if ($this instanceof User)
		{
			$person = $this->getPerson();
			if ($person instanceof Person)
				return $person->formatName($options);
			else
				return $this->getUsername();
		}

		if ($this instanceof Staff)
		{
			$person = $this->getPerson();
			if ($person instanceof Person)
				return $person->formatName($options);
		}

		if ($this instanceof Student)
		{
			$person = $this->getPerson();
			if (empty($options))
			{
				$options['preferredOnly'] = true;
				$options['surnameFirst']  = false;
			}
			if ($person instanceof Person)
				return $person->formatName($options);
		}

		throw new CommonException('The record ' . __CLASS__ . ' does not have a valid person.');
	}
}
