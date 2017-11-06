<?php
namespace Busybee\People\PersonBundle\Model;

use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\Core\SecurityBundle\Security\UserProvider;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\People\ContactBundle\Entity\Contact;
use Busybee\Program\GradeBundle\Entity\StudentGrade;
use Busybee\People\FamilyBundle\Entity\CareGiver;
use Busybee\People\FamilyBundle\Entity\Family;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class PersonManager
{
	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * @var SettingManager
	 */
	private $sm;

	/**
	 * @var null|Person
	 */
	private $person;

	/**
	 * @var UserProvider
	 */
	private $userProvider;

	/**
	 * PersonManager constructor.
	 *
	 * @param ObjectManager  $om
	 * @param SettingManager $sm
	 * @param UserProvider   $userProvider
	 */
	public function __construct(ObjectManager $om, SettingManager $sm, UserProvider $userProvider)
	{
		$this->om           = $om;
		$this->sm           = $sm;
		$this->person       = null;
		$this->userProvider = $userProvider;
	}

	/**
	 * @param Person $person
	 *
	 * @return bool
	 */
	public function canDeleteStaff(Person $person = null)
	{
		$this->checkPerson($person);

		if (!$this->person instanceof Staff)
			return false;

		return $this->person->canDelete();
	}

	/**
	 * @param null|string|integer $id
	 *
	 * @return Person|null
	 */
	public function getPerson($id = null)
	{
		if (!is_null($id))
			$this->person = $this->find($id);

		return $this->person;
	}

	/**
	 * @param Person $person
	 *
	 * @return PersonManager
	 */
	public function setPerson(Person $person): PersonManager
	{
		$this->person = $person;

		return $this;
	}

	/**
	 * @param $id
	 *
	 * @return Person|null
	 */
	public function find($id)
	{
		$this->person = new Person();

		if ($id !== 'Add')
			$this->person = $this->om->getRepository(Person::class)->find($id);

		return $this->person;
	}

	/**
	 * @param Person $person
	 *
	 * @return ArrayCollection
	 */
	public function getAddresses(Person $person): ArrayCollection
	{
		$families = $this->getFamilies($person);

		$addresses = new ArrayCollection();
		foreach ($families as $family)
		{
			$address = $family->getAddress1();
			if (!is_null($address) && !$addresses->contains($address))
				$addresses->add($address);
			$address = $family->getAddress2();
			if (!is_null($address) && !$addresses->contains($address))
				$addresses->add($address);
		}

		return $addresses;
	}

	/**
	 * @param Person $person
	 *
	 * @return ArrayCollection
	 */
	public function getFamilies(Person $person = null): ArrayCollection
	{
		$this->checkPerson($person);

		$families = new ArrayCollection();
		if ($this->familyInstalled())
		{
			$careGivers = $this->om->getRepository(Family::class)->findByCareGiverPerson($this->person);
			if (!empty($careGivers))
				foreach ($careGivers as $family)
					if (!$families->contains($family))
						$families->add($family);

			$students = $this->om->getRepository(Family::class)->findByStudentsPerson($this->person);
			if (!empty($students))
				foreach ($students as $family)
					if (!$families->contains($family))
						$families->add($family);
		}

		return $families;
	}

	/**
	 * @param Person $person
	 *
	 * @param bool   $all
	 *
	 * @return ArrayCollection
	 */
	public function getPhones(Person $person, $all = false): ArrayCollection
	{
		$families = $this->getFamilies($person);
		$phones   = new ArrayCollection();

		foreach ($families as $family)
		{
			foreach ($family->getPhone() as $phone)
				if (!$phones->contains($phone)) $phones->add($phone);
		}

		if ($all)
			foreach ($person->getPhone() as $phone)
				if (!$phones->contains($phone)) $phones->add($phone);

		return $phones;
	}

	/**
	 * @param Person $person
	 *
	 * @return bool
	 */
	public function canBeStaff(Person $person = null): bool
	{
		$person = $this->checkPerson($person);
		//place rules here to stop new staff.
		if ($this->isStudent())
			return false;

		return true;
	}

	/**
	 * @param Person|null $person
	 *
	 * @return bool
	 */
	public function isStudent(Person $person = null): bool
	{
		$this->checkPerson($person);

		if ($this->person instanceof Student)
			return true;

		return false;
	}

	/**
	 * @param Person $person
	 *
	 * @return bool
	 */
	public function canBeStudent(Person $person = null): bool
	{
		$this->checkPerson($person);
		//place rules here to stop new student.

		if ($this->isStaff() || $this->isCareGiver())
			return false;

		return true;
	}

	/**
	 * @param Person $person
	 *
	 * @return bool
	 */
	public function isStaff(Person $person = null): bool
	{
		$this->checkPerson($person);

		if ($this->person instanceof Staff)
			return true;

		return false;
	}

	/**
	 * @param Person $person
	 *
	 * @return bool
	 */
	public function isCareGiver(Person $person = null): bool
	{
		$person = $this->checkPerson($person);

		if (!$this->om->getMetadataFactory()->hasMetadataFor(CareGiver::class))
			return false;

		$careGiver = $this->om->getRepository(CareGiver::class)->findOneByPerson($person);
		if ($careGiver instanceof CareGiver)
			return true;

		return false;
	}

	/**
	 * @param null|Person $person
	 *
	 * @return bool
	 */
	public function canBeUser(Person $person = null)
	{
		$person = $this->checkPerson($person);
		if (empty($person->getEmail()))
			return false;

		return true;
	}

	/**
	 * @param Person $person
	 *
	 * @return bool
	 */
	public function isUser(Person $person = null)
	{
		$person = $this->checkPerson($person);
		if (is_null($person->getUser()))
			return false;

		$user = $this->om->getRepository(User::class)->find($person->getUser()->getId());
		if ($user instanceof User)
			return true;

		return false;
	}

	/**
	 * @param Person|null $person
	 *
	 * @return bool
	 */
	public function validPerson(Person $person = null)
	{
		$person = $this->checkPerson($person);

		if ($person instanceof Person && intval($person->getId()) > 0)
			return true;

		return false;
	}

	/**
	 * @param   Person $person
	 * @param   array  $parameters
	 *
	 * @return  bool
	 */
	public function canDeleteStudent(Person $person = null, $parameters = []): bool
	{
		$student = $this->checkPerson($person);

		//Place rules here to stop delete .
		if (!$student instanceof Student)
			return false;

		$families = $this->getFamilies($student);

		if (is_array($families) && count($families) > 0)
			return false;
		if ($this->gradesInstalled())
		{
			$grades = $this->om->getRepository(StudentGrade::class)->findAll(['status' => 'Current', 'student' => $student->getId()]);

			if (is_array($grades) && count($grades) > 0)
				return false;
		}

		if (is_array($parameters))
			foreach ($parameters as $data)
				if (isset($data['data']['name']) && isset($data['entity']['name']))
				{
					$client = $this->om->getRepository($data['entity']['name'])->findOneByStudent($student->getId());

					if (is_object($client) && $client->getId() > 0)
						return false;
				}

		return $student->canDelete();
	}

	/**
	 * @param Person $person
	 *
	 * @return bool
	 */
	public function canDeleteUser(Person $person = null): bool
	{
		$person = $this->checkPerson($person);

		return $this->userProvider->getUserManager()->canDeleteUser($person->getUser());
	}

	/**
	 * @param Person|null $person
	 *
	 * @return Person
	 */
	private function checkPerson(Person $person = null): Person
	{
		if ($person instanceof Person)
		{
			$this->person = $person;

			return $this->person;
		}

		if ($this->person instanceof Person)
			return $this->person;

		$this->person = $this->getPerson('Add');

		return $this->person;
	}

	/**
	 * Create Staff
	 *
	 * @param Person|null $person
	 * @param bool        $persist
	 *
	 * @return bool
	 */
	public function createStaff(Person $person = null, $persist = false)
	{
		$this->checkPerson($person);

		if ($this->canBeStaff())
		{
			$tableName = $this->om->getClassMetadata(Person::class)->getTableName();

			$this->om->getConnection()->exec('UPDATE `' . $tableName . '` SET `person_type` = "staff" WHERE `' . $tableName . '`.`id` = ' . strval(intval($this->person->getId())));

			if ($persist)
			{
				$this->om->persist($this->person);
				$this->om->flush();
			}

			$this->om->refresh($this->person);

			return true;
		}

		return false;
	}

	/**
	 * Create Staff
	 *
	 * @param Person|null $person
	 */
	public function deleteStaff(Person $person = null)
	{
		$this->checkPerson($person);

		if ($this->canDeleteStaff())
		{
			$this->switchToPerson();
		}
	}

	/**
	 * Create Student
	 *
	 * @param Person|null $person
	 * @param bool        $persist
	 *
	 * @return bool
	 */
	public function createStudent(Person $person = null, $persist = false)
	{
		$this->checkPerson($person);

		if ($this->canBeStudent())
		{
			$this->switchToStudent();

			return true;
		}

		return false;
	}

	/**
	 * Create Staff
	 *
	 * @param Person|null $person
	 */
	public function deleteStudent(Person $person = null)
	{
		$this->checkPerson($person);

		if ($this->canDeleteStudent())
		{
			$this->switchToPerson();
		}
	}

	/**
	 * @return ObjectManager
	 */
	public function getOm(): ObjectManager
	{
		return $this->om;
	}

	/**
	 * @return SettingManager
	 */
	public function getSm(): SettingManager
	{
		return $this->sm;
	}

	/**
	 * @return UserProvider
	 */
	public function getUserProvider(): UserProvider
	{
		return $this->userProvider;
	}

	/**
	 * @return bool
	 */
	public function gradesInstalled(): bool
	{
		if (class_exists('Busybee\Program\GradeBundle\Model\GradeManager'))
		{
			$metaData = $this->getOm()->getClassMetadata('Busybee\Program\GradeBundle\Entity\StudentGrade');
			$schema   = $this->getOm()->getConnection()->getSchemaManager();

			return $schema->tablesExist([$metaData->table['name']]);

		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function familyInstalled(): bool
	{
		if (class_exists('Busybee\People\FamilyBundle\Model\FamilyManager'))
		{
			$metaData = $this->getOm()->getClassMetadata('Busybee\People\FamilyBundle\Entity\Family');
			$schema   = $this->getOm()->getConnection()->getSchemaManager();

			return $schema->tablesExist([$metaData->table['name']]);

		}

		return false;
	}

	/**
	 * Get Details
	 *
	 * @param Person $person
	 *
	 * @return string
	 */
	public function getDetails(Person $person)
	{
		$result = '';

		if ($person instanceof Staff && !empty($person->getHonorific()))
			$result .= $person->getHonorific() . '<br/>';

		if ($person instanceof Student && !empty($this->getCurrentGrade($person)))
			$result .= $this->getCurrentGrade($person) . '<br/>';

		if (!empty($person->getEmail()))
			$result .= $person->getEmail() . '<br/>';
		if (!empty($person->getEmail2()))
			$result .= $person->getEmail2() . '<br/>';

		foreach ($this->getPhones($person, true) as $phone)
		{
			$x = $this->getSm()->get('phone.display', null, ['phone' => $phone->getPhoneNumber()]);
			if (!empty($x))
				$result .= str_replace("\n", "<br/>", $x);
		}

		return $result;
	}

	/**
	 * Get Current Grade
	 *
	 * @param $person
	 *
	 * @return string
	 */
	public function getCurrentGrade($person)
	{
		if (!$person instanceof Student || !$this->gradesInstalled())
			return null;

		foreach ($person->getGrades()->toArray() as $grade)
			if ($grade->getStatus() == 'Current')
				return $grade->getGradeYear();

		return null;
	}

	/**
	 * @return bool
	 */
	public function departmentInstalled(): bool
	{
		if (class_exists('Busybee\Facility\InstituteBundle\Model\DepartmentManager'))
		{
			$metaData = $this->getOm()->getClassMetadata('Busybee\Facility\InstituteBundle\Entity\DepartmentStaff');
			$schema   = $this->getOm()->getConnection()->getSchemaManager();

			return $schema->tablesExist([$metaData->table['name']]);

		}

		return false;
	}

	/**
	 * Switch to Student
	 *
	 * @param Person|null $person
	 * @param bool        $persist
	 *
	 * @return Person|null
	 */
	public function switchToStudent(Person $person = null, $persist = false)
	{
		$this->checkPerson($person);

		if ($this->person instanceof Person && !is_subclass_of($this->person, Person::class))
		{
			$tableName = $this->om->getClassMetadata(Person::class)->getTableName();

			$x = $this->om->getConnection()->exec('UPDATE `' . $tableName . '` SET `person_type` = "student" WHERE `' . $tableName . '`.`id` = ' . strval(intval($this->person->getId())));

			if ($persist)
			{
				$this->om->persist($this->person);
				$this->om->flush();
			}

			$this->om->refresh($this->person);

		}

		return $this->person;
	}

	/**
	 * Switch to Staff
	 *
	 * @param Person|null $person
	 * @param bool        $persist
	 *
	 * @return Person|null
	 */
	public function switchToStaff(Person $person = null, $persist = false)
	{
		$this->checkPerson($person);

		if ($this->person instanceof Person && !is_subclass_of($this->person, Person::class))
		{
			$tableName = $this->om->getClassMetadata(Person::class)->getTableName();

			$x = $this->om->getConnection()->exec('UPDATE `' . $tableName . '` SET `person_type` = "staff" WHERE `' . $tableName . '`.`id` = ' . strval(intval($this->person->getId())));

			if ($persist)
			{
				$this->om->persist($this->person);
				$this->om->flush();
			}

			$this->om->refresh($this->person);

		}

		return $this->person;
	}

	/**
	 * Switch to Staff
	 *
	 * @param Person|null $person
	 * @param bool        $persist
	 *
	 * @return Person|null
	 */
	public function switchToPerson(Person $person = null, $persist = false)
	{
		$this->checkPerson($person);

		if (is_subclass_of($this->person, Person::class))
		{
			$tableName = $this->om->getClassMetadata(Person::class)->getTableName();

			$x = $this->om->getConnection()->exec('UPDATE `' . $tableName . '` SET `person_type` = "person" WHERE `' . $tableName . '`.`id` = ' . strval(intval($this->person->getId())));

			if ($persist)
			{
				$this->om->persist($this->person);
				$this->om->flush();
			}

			$this->om->refresh($this->person);

		}

		return $this->person;
	}

	/**
	 * Switch to Staff
	 *
	 * @param Person|null $person
	 * @param bool        $persist
	 *
	 * @return Person|null
	 */
	public function switchToContact(Person $person = null, $persist = false)
	{
		$this->checkPerson($person);

		if ($this->person instanceof Person && !is_subclass_of($this->person, Person::class))
		{
			$tableName = $this->om->getClassMetadata(Person::class)->getTableName();

			$x = $this->om->getConnection()->exec('UPDATE `' . $tableName . '` SET `person_type` = "contact" WHERE `' . $tableName . '`.`id` = ' . strval(intval($this->person->getId())));

			if ($persist)
			{
				$this->om->persist($this->person);
				$this->om->flush();
			}

			$this->om->refresh($this->person);

		}

		return $this->person;
	}
}