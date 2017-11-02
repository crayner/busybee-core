<?php

namespace Busybee\People\StaffBundle\Model;

use Busybee\Facility\InstituteBundle\Entity\DepartmentStaff;
use Busybee\People\PersonBundle\Entity\Person;

abstract class StaffModel extends Person
{
	/**
	 * @param string $float
	 *
	 * @return mixed
	 */
	public function getPortrait($float = 'none')
	{
		return $this->getPerson()->getPhoto75($float);
	}

	/**
	 * @return string
	 */
	public function getDepartments()
	{
		$depts  = $this->getDepartment();
		$string = '';
		foreach ($depts as $dept)
		{
			if ($dept instanceof DepartmentStaff)
				$string .= $dept->getDepartment()->getName() . ', ';
		}

		return trim($string, ', ');
	}

	/**
	 * @todo Check if a Staff record can be deleted
	 */
	public function canDelete()
	{
		// Add Staff Delete checks here.

		return parent::canDelete();
	}
}