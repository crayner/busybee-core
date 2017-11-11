<?php

namespace GibbonBundle\Model;

use Busybee\Core\HomeBundle\Exception\Exception;
use Busybee\People\FamilyBundle\Entity\CareGiver;
use Busybee\People\FamilyBundle\Entity\Family;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class ImportFamily extends ImportManager
{
	/**
	 * @var int
	 */
	private $familyCount;

	/**
	 * @var int
	 */
	private $offset;

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

		$sql = "SELECT COUNT(`gibbonFamilyID`) as `CC` FROM `gibbonFamily`";

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$this->familyCount = $stmt->fetch();
		$this->familyCount = $this->familyCount['CC'];

		return $this;
	}

	public function importFamily(int $offset): ImportFamily
	{
		$sql = "SELECT * FROM `gibbonFamily` LIMIT " . $offset . "," . $this->getLimit();

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$families = $stmt->fetchAll();

		if (count($families) < 1)
		{
			$this->offset = 0;

			return $this;
		}

		foreach ($families as $gibbonFamily)
		{
			$family = $this->getFamily($gibbonFamily);

			foreach ($gibbonFamily as $name => $value)
			{
				switch ($name)
				{
					case 'gibbonFamilyID':
						$family->setImportIdentifier(intval($value));
						break;
					case 'languageHomePrimary':
						if (!empty($value))
							$family->setFirstLanguage(intval($value));
						break;
					case 'languageHomeSecondary':
						if (!empty($value))
							$family->setSecondLanguage(intval($value));
						break;
					case 'status':
						if (!empty($value))
							$family->setStatus($value);
						break;
					case 'homeAddress':
						if (!empty($value))
							$this->buildAddress($family, $gibbonFamily, '1');
					case 'homeAddressDistrict':
					case 'homeAddressCountry':
						break;
					case 'nameAddress':
						if (!empty($value) && $value != strval(intval($value)))
							$family->setName($value);
						break;
					default:
						//dump([$name, $value]);
				}

			}

			//Caregivers
			$sql = "SELECT * FROM `gibbonFamilyAdult` WHERE `gibbonFamilyID` =" . $family->getImportIdentifier() . " ORDER BY `contactPriority` ASC";

			$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
			$stmt->execute();
			$adults = $stmt->fetchAll();

			foreach ($adults as $adult)
			{
				$person = $this->getPerson($adult);

				if ($person instanceof Person)
				{
					$caregiver = $this->getCareGiver($family, $person);

					foreach ($adult as $name => $value)
					{
						switch ($name)
						{
							case 'gibbonFamilyAdultID':
							case 'gibbonFamilyID':
							case 'gibbonPersonID':
								break;
							case 'comment':
								if (!empty($value))
									$caregiver->addComment($value);
								break;
							case 'childDataAccess':
								if ($value == 'Y')
								{
									$caregiver->setReporting(true);
									$caregiver->setNewsletter(true);
									$caregiver->setFinance(true);
								}
								else
								{
									$caregiver->setReporting(false);
									$caregiver->setNewsletter(false);
									$caregiver->setFinance(false);
								}
								break;
							case 'contactPriority':
								$caregiver->setContactPriority($value);
								break;
							case 'contactCall':
								if ($value == 'Y')
									$caregiver->setPhoneContact(true);
								else
									$caregiver->setPhoneContact(false);
								break;
							case 'contactSMS':
								if ($value == 'Y')
									$caregiver->setSmsContact(true);
								else
									$caregiver->setSmsContact(false);
								break;
							case 'contactEmail':
								if ($value == 'Y')
									$caregiver->setEmailContact(true);
								else
									$caregiver->setEmailContact(false);
								break;
							case 'contactMail':
								if ($value == 'Y')
									$caregiver->setMailContact(true);
								else
									$caregiver->setMailContact(false);
								break;
							default:
								//dump([$name,$value]);
						}
					}
					$family->addCareGiver($caregiver);
				}
			}


			$sql = "SELECT * FROM `gibbonFamilyChild` WHERE `gibbonFamilyID` = " . $family->getImportIdentifier();

			$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
			$stmt->execute();
			$children = $stmt->fetchAll();

			foreach ($children as $child)
			{
				$student = $this->getPerson($child);

				if ($student instanceof Student)
				{
					if (!empty($child['comment']))
						$student->setComment($child['comment']);
					$family->addStudent($student);
				}
			}

			if ($family->getCareGivers()->count() > 0 && $family->getStudents()->count() > 0)
			{
				$family->checkFamilyName();

				$this->getDefaultManager()->persist($family);
				$this->getDefaultManager()->flush();
			}
			else
				$this->getMessages()->addMessage('warning', 'family.import.failed', ['%{id}' => $family->getImportIdentifier()]);
			ini_set('max_execution_time', '10');
		}
		$this->offset = $offset + count($families);

		return $this;
	}

	/**
	 * @param $data
	 *
	 * @return Family
	 */
	private function getFamily($data): Family
	{
		$family = $this->getDefaultManager()->getRepository(Family::class)->findOneByImportIdentifier(intval($data['gibbonFamilyID']));

		if (!$family instanceof Family)
			$family = new Family();

		return $family;
	}

	/**
	 * @param        $person
	 * @param        $gibbonPerson
	 * @param string $address
	 */
	private function buildAddress($family, $gibbonFamily, $address = '1')
	{
		throw new Exception('todo Here');
	}

	/**
	 * @param $data
	 *
	 * @return Family
	 */
	private function getPerson($data): ?Person
	{
		$person = $this->getDefaultManager()->getRepository(Person::class)->findOneByImportIdentifier(intval($data['gibbonPersonID']));

		if (!$person instanceof Person)
			$person = null;

		return $person;
	}

	/**
	 * @param $family
	 * @param $person
	 *
	 * @return CareGiver
	 */
	private function getCaregiver($family, $person): CareGiver
	{
		$caregiver = $this->getDefaultManager()->getRepository(CareGiver::class)->findOneBy(['person' => $person, 'family' => $family]);

		if (!$caregiver instanceof CareGiver)
			$caregiver = new CareGiver();

		$caregiver->setPerson($person);
		$caregiver->setFamily($family);

		return $caregiver;
	}

	/**
	 * @return int
	 */
	public function getFamilyCount(): ?int
	{
		return $this->familyCount;
	}

	/**
	 * @return int
	 */
	public function getOffset(): int
	{
		if (empty($this->offset))
			$this->offset = 0;

		return $this->offset;
	}
}