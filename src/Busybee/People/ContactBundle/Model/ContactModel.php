<?php

namespace Busybee\People\ContactBundle\Model;

use Busybee\Facility\InstituteBundle\Entity\DepartmentStaff;
use Busybee\People\PersonBundle\Entity\Person;

abstract class ContactModel extends Person
{
	/**
	 * Can Delete
	 *     * @todo Check if a Contact record can be deleted
	 * @return  bool
	 */
	public function canDelete(): bool
	{
		return parent::canDelete();
	}
}