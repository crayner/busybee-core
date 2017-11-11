<?php

namespace Busybee\People\FamilyBundle\Model;

use Busybee\People\PersonBundle\Entity\Person;

class CareGiverModel
{
	public function __construct()
	{
		$this->setPhoneContact(false);
		$this->setSmsContact(false);
		$this->setMailContact(false);
		$this->setEmailContact(false);
		$this->setContactPriority(0);
		$this->setRelationship('Unknown');
	}

	public function __toString()
	{
		return strval($this->getId());
	}

	public function formatName($options = []): string
	{
		if ($this->getPerson() instanceof Person)
		{
			return $this->getPerson()->formatName($options);
		}

		return '';
	}
}