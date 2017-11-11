<?php

namespace GibbonBundle\Model;

use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Intl\Intl;

class ImportPeople extends ImportManager
{
	/**
	 * @var int
	 */
	private $peopleCount;

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

		$sql = "SELECT COUNT(`gibbonPersonID`) as `CC` FROM `gibbonPerson`";

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$this->peopleCount = $stmt->fetch();
		$this->peopleCount = $this->peopleCount['CC'];

		return $this;
	}

	/**
	 * Import People
	 */
	public function importPeople(int $offset): ImportPeople
	{
		$sql = "SELECT * FROM `gibbonPerson` LIMIT " . $offset . "," . $this->getLimit();

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$people = $stmt->fetchAll();

		$extra = [];
		if (count($people) < 1)
		{
			$this->offset = 0;

			return $this;
		}
		foreach ($people as $gibbonPerson)
		{
			$person = $this->getPerson($gibbonPerson);

			foreach ($gibbonPerson as $name => $value)
			{
				switch ($name)
				{
					case 'gibbonPersonID':
						$person->setImportIdentifier(intval($value));
						break;
					case 'title':
						if (!empty($value))
							$person->setHonorific($value);
						break;
					case 'surname':
						if (!empty($value))
							$person->setSurname($value);
						break;
					case 'firstName':
						if (!empty($value))
							$person->setFirstName($value);
						break;
					case 'preferredName':
						if (!empty($value))
							$person->setPreferredName($value);
						break;
					case 'officialName':
						$value = $person->getFirstName() . ' ' . $person->getSurname();
						$person->setOfficialName($value);
						break;
					case 'nameInCharacters':
						if (!empty($value))
							$person->setNameInCharacters($value);
						break;
					case 'gender':
						if (!empty($value))
							$person->setGender($value);
						break;
					case 'status':
						if (!empty($value) && method_exists($person, 'setStatus'))
							$person->setStatus($value);
						break;
					case 'dob':
						if (!empty($value))
							$person->setDob(new \DateTime($value));
						break;
					case 'email':
						if (!empty($value))
							$person->setEmail($value);
						break;
					case 'emailAlternate':
						if (!empty($value))
							$person->setEmail2($value);
						break;
					case 'address1':
						if (!empty($value))
							$extra[] = $this->buildAddress($person, $gibbonPerson);
						break;
					case 'address1Country':
					case 'address1District':
						break;
					case 'address2':
						if (!empty($value))
							$extra[] = $this->buildAddress($person, $gibbonPerson, '2');
						break;
					case 'address2Country':
					case 'address2District':
						break;
					case 'phone1':
						if (!empty($value))
							$extra[] = $this->buildPhone($person, $gibbonPerson, '1');
						break;
					case 'phone1Type':
					case 'phone1CountryCode':
						break;
					case 'phone2':
						if (!empty($value))
							$extra[] = $this->buildPhone($person, $gibbonPerson, '2');
						break;
					case 'phone2Type':
					case 'phone2CountryCode':
						break;
					case 'phone3':
						if (!empty($value))
							$extra[] = $this->buildPhone($person, $gibbonPerson, '3');
						break;
					case 'phone3Type':
					case 'phone3CountryCode':
						break;
					case 'phone4':
						if (!empty($value))
							$extra[] = $this->buildPhone($person, $gibbonPerson, '4');
						break;
					case 'phone4Type':
					case 'phone4CountryCode':
						break;
					case 'phone5':
						if (!empty($value))
							$extra[] = $this->buildPhone($person, $gibbonPerson, '5');
						break;
					case 'phone5Type':
					case 'phone5CountryCode':
						break;
					case 'website':
						if (!empty($value))
							$person->setWebsite($value);
						break;
					case 'languageFirst':
						if (!empty($value))
							$person->setFirstLanguage($value);
						break;
					case 'languageSecond':
						if (!empty($value))
							$person->setSecondLanguage($value);
						break;
					case 'languageThird':
						if (!empty($value))
							$person->setThirdLanguage($value);
						break;
					case 'countryOfBirth':
						if (!empty($value))
						{
							$countries = Intl::getRegionBundle()->getCountryNames();
							$value     = array_search($value, $countries);
							if (!$value)
								$person->setCountryOfBirth($value);
						}
						break;
					case 'ethnicity':
						if (!empty($value))
							$person->setEthnicity($value);
						break;
					case 'citizenship1':
						if (!empty($value))
						{
							$countries = Intl::getRegionBundle()->getCountryNames();
							$value     = array_search($value, $countries);
							if (!$value)
								$person->setCitizenship1($value);
						}
						break;
					case 'citizenship1Passport':
						if (!empty($value))
							$person->setCitizenship1Passport($value);
						break;
					case 'citizenship1PassportScan':
						if (!empty($value))
							$person->setCitizenship1PassportScan($value);
						break;
					case 'citizenship2':
						if (!empty($value))
						{
							$countries = Intl::getRegionBundle()->getCountryNames();
							$value     = array_search($value, $countries);
							if (!$value)
								$person->setsetCitizenship2($value);
						}
						break;
					case 'citizenship2Passport':
						if (!empty($value))
							$person->setCitizenship2Passport($value);
						break;
					case 'citizenship2PassportScan':
						if (!empty($value))
							$person->setCitizenship2PassportScan($value);
						break;
					case 'birthCertificateScan':
						if (!empty($value))
							$person->setBirthCertificateScan($value);
						break;
					case 'religion':
						if (!empty($value))
							$person->setReligion($value);
						break;
					case 'nationalIDCardNumber':
						if (!empty($value))
							$person->setNationalIDCardNumber($value);
						break;
					case 'nationalIDCardScan':
						if (!empty($value))
							$person->setNationalIDCardScan($value);
						break;
					case 'residencyStatus':
						if (!empty($value))
							$person->setResidencyStatus($value);
						break;
					case 'visaExpiryDate':
						if (!empty($value))
							$person->setVisaExpiryDate(new \DateTime($value));
						break;
					case 'profession':
						if (!empty($value))
							$person->setProfession($value);
						break;
					case 'employer':
						if (!empty($value))
							$person->setEmployer($value);
						break;
					case 'jobTitle':
						if (!empty($value))
							$person->setJobTitle($value);
						break;
					case 'gibbonHouseID':
						if (!empty($value))
							$this->importHouse($person, $value);
						break;
					case 'image_240':
						if (!empty($value))
							$person->setPhoto($value);
						break;
					case 'studentID':
						if (!empty($value))
							$person->setIdentifier($value);
						break;
					case 'dateStart':
						if (!empty($value))
							$person->setStartAtSchool($value);
						break;
					case 'dateEnd':
						if (!empty($value))
							$person->setLastAtThisSchool($value);
						break;
					case 'lastSchool':
						if (!empty($value))
							$person->setLastSchool($value);
						break;
					case 'nextSchool':
						if (!empty($value))
							$person->setNextSchool($value);
						break;
					case 'departureReason':
						if (!empty($value))
							$person->setDepartureReason($value);
						break;
					case 'transport':
						if (!empty($value))
							$person->setTransport($value);
						break;
					case 'transportNotes':
						if (!empty($value))
							$person->setTransportNotes($value);
						break;
					case 'lockerNumber':
						if (!empty($value))
							$person->setLocker($value);
						break;
					case 'vehicleRegistration':
						if (!empty($value))
							$person->setVehicleRegistration($value);
						break;
					case 'dayType':
						if (!empty($value))
							$person->setDayType($value);
						break;
					default:
						//dump([$name, $value]);
				}
			}

			$this->getDefaultManager()->persist($person);
			$this->getDefaultManager()->flush();
			ini_set('max_execution_time', '10');
		}
		$this->offset = $offset + count($people);

		return $this;
	}

	/**
	 * @param $data
	 *
	 * @return Person
	 */
	private function getPerson($data): Person
	{
		$person = $this->getDefaultManager()->getRepository(Person::class)->findOneByImportIdentifier(intval($data['gibbonPersonID']));

		$sql = "SELECT * FROM `gibbonStaff` WHERE `gibbonPersonID` = " . $data['gibbonPersonID'];

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$staff = $stmt->fetch();
		if ($staff && $person instanceof Person)
			$person = $this->getPersonManager()->switchToStaff($person);
		elseif ($staff)
			$person = new Staff();

		$sql = "SELECT * FROM `gibbonStudentEnrolment` WHERE `gibbonPersonID` = " . $data['gibbonPersonID'];

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$student = $stmt->fetch();

		if ($student && $person instanceof Person)
			$person = $this->getPersonManager()->switchToStudent($person);
		elseif ($student)
			$person = new Student();

		if (!$person instanceof Person)
			$person = new Person();

		return $person;
	}

	/**
	 * @param        $person
	 * @param        $gibbonPerson
	 * @param string $address
	 */
	private function buildAddress($person, $gibbonPerson, $address = '1')
	{
		throw new Exception('todo Here');
	}

	/**
	 * @param        $person
	 * @param        $gibbonPerson
	 * @param string $phone
	 */
	private function buildPhone($person, $gibbonPerson, $phone = '1')
	{
		throw new Exception('todo Here');
	}

	/**
	 * @param $person
	 * @param $value
	 */
	private function importHouse($person, $value)
	{
		$sql = "SELECT `name` FROM `gibbonHouse` WHERE `gibbonHouseID` = '" . $value . "'";

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$house = $stmt->fetch();

		if (!empty($house['name']))
			if (method_exists($person, 'setHouse'))
				$person->setHouse($house['name']);
	}

	/**
	 * @return int
	 */
	public function getOffset(): int
	{
		return $this->offset;
	}

	/**
	 * @return int
	 */
	public function getPeopleCount(): int
	{
		return $this->peopleCount;
	}
}