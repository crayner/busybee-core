<?php
namespace Busybee\People\PersonBundle\Model;

class PersonManager
{
	/**
	 * @param Person $person
	 *
	 * @return bool
	 */
	public function canDeleteStaff(Person $person)
	{
		return $person->canDelete();
	}
}