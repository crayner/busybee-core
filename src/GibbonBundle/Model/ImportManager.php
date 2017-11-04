<?php

namespace GibbonBundle\Model;

use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\People\StaffBundle\Entity\Staff;
use Doctrine\Common\Persistence\ObjectManager;

class ImportManager
{
	/**
	 * @var ObjectManager
	 */
	private $gibbonManager;
	/**
	 * @var ObjectManager
	 */
	private $manager;
	/**
	 * @var PersonManager
	 */
	private $personManager;

	public function __construct(ObjectManager $gibbonManager, ObjectManager $manager, PersonManager $personManager)
	{
		$this->gibbonManager = $gibbonManager;
		$this->manager       = $manager;
		$this->personManager = $personManager;
	}

	public function buildAddress($person, $gibbonPerson, $address = '1')
	{
		dump([$person, $gibbonPerson, $address]);
		die();
	}

	public function getDefaultManager(): ObjectManager
	{
		return $this->manager;
	}

	public function getPerson($data): Person
	{
		$person = $this->manager->getRepository(Person::class)->findOneByImportIdentifier(intval($data['gibbonPersonID']));

		$sql = "SELECT * FROM `gibbonStaff` WHERE `gibbonPersonID` = " . $data['gibbonPersonID'];

		$stmt = $this->gibbonManager->getConnection()->prepare($sql);
		$stmt->execute();
		$staff = $stmt->fetch();
		if ($staff && $person instanceof Person)
			$person = $this->personManager->switchToStaff($person);
		elseif ($staff)
			$person = new Staff();

		$sql = "SELECT * FROM `gibbonStudentEnrolment` WHERE `gibbonPersonID` = " . $data['gibbonPersonID'];

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$student = $stmt->fetch();

		if ($student && $person instanceof Person)
			$person = $this->personManager->switchToStudent($person);
		elseif ($student)
			$person = new Student();

		if (!$person instanceof Person)
			$person = new Person();

		return $person;
	}

	public function getGibbonManager(): ObjectManager
	{
		return $this->gibbonManager;
	}
}