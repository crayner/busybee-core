<?php

namespace Busybee\People\PersonBundle\Model;

use Busybee\People\PersonBundle\Entity\Person;

interface PersonInterface
{

	/**
	 * Get Person
	 *
	 * @return Person
	 */
	public function getPerson();

	/**
	 * Format Name
	 *
	 * @param array $options
	 *
	 * @return string
	 */
	public function formatName($options = []);
}