<?php

namespace Busybee\People\StaffBundle\Model;

use Busybee\InstituteBundle\Entity\DepartmentStaff;
use Busybee\People\PersonBundle\Model\PersonModel;

abstract class StaffModel extends PersonModel
{
	public function __construct()
	{
		$this->setStaffType('Unknown');
		$this->setJobTitle('Not Specified');
	}

	public function canDelete()
	{
		return true;
	}

	/**
	 * @param string $float
	 *
	 * @return mixed
	 */
	public function getPortrait($float = 'none')
	{
		return $this->getPerson()->getPhoto75($float);
	}

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
}