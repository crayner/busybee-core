<?php

namespace Busybee\People\StaffBundle\Repository;

use Busybee\People\PersonBundle\Repository\PersonRepository;
use Busybee\People\StaffBundle\Entity\Staff;

/**
 * StaffRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StaffRepository extends PersonRepository
{
	/**
	 * @param   integer $personID
	 *
	 * @return  Staff
	 */
	public function findOneByPerson($personID)
	{
		$staff = parent::find($personID);

		return $staff instanceof Staff ? $staff : new Staff();
	}

}
