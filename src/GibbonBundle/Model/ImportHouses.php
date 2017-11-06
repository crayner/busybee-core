<?php

namespace GibbonBundle\Model;

use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Intl\Intl;

class ImportHouses extends ImportManager
{
	/**
	 * ImportPeople constructor.
	 *
	 * @param ObjectManager $gibbonManager
	 * @param ObjectManager $manager
	 * @param PersonManager $personManager
	 */
	public function __construct(ObjectManager $gibbonManager, ObjectManager $manager, PersonManager $personManager)
	{
		parent::__construct($gibbonManager, $manager, $personManager);

		$sql = "SELECT * FROM `gibbonHouse` ORDER BY `name`";

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$houses = $stmt->fetchAll();

		$list = [];

		foreach ($houses as $house)
		{
			$list[$house['name']]['name']      = $house['name'];
			$list[$house['name']]['shortName'] = $house['nameShort'];
			$list[$house['name']]['logo']      = $house['logo'];
		}

		$this->getPersonManager()->getSm()->set('house.list', $list);
	}
}