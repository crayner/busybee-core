<?php
namespace Busybee\People\PersonBundle\Model;

use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\Core\SecurityBundle\Security\UserProvider;
use Busybee\Core\SystemBundle\Setting\SettingManager;
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
	 * @param ObjectManager $om
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

		if ($this->om->getMetadataFactory()->hasMetadataFor(Family::class))
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
	 * @return ArrayCollection
	 */
	public function getPhones(Person $person): ArrayCollection
	{
		$families = $this->getFamilies($person);
		$phones   = new ArrayCollection();

		foreach ($families as $family)
		{
			foreach ($family->getPhone() as $phone)
				if (!$phones->contains($phone)) $phones->add($phone);
		}

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
		//place rules here to stop new student.
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
		$person = $this->checkPerson($person);

		return $person->isStudent();
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

		return !$this->isStudent();
	}

	/**
	 * @param Person $person
	 *
	 * @return bool
	 */
	public function isStaff(Person $person = null): bool
	{
		$person = $this->checkPerson($person);

		return $person->isStaff();
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

	public function validPerson(Person $person)
	{
		if (intval($person->getId()) > 0)
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

		$grades = $this->om->getRepository(StudentGrade::class)->findAll(['status' => 'Current', 'student' => $student->getId()]);

		if (is_array($grades) && count($grades) > 0)
			return false;


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
	 */
	public function createStaff(Person $person = null)
	{
		$this->checkPerson($person);

		if ($this->canBeStaff())
		{
			$tableName = $this->om->getClassMetadata(Person::class)->getTableName();

			$this->om->getConnection()->exec('UPDATE `' . $tableName . '` SET `person_type` = "staff" WHERE `' . $tableName . '`.`id` = ' . strval(intval($this->person->getId())));

			$this->om->refresh($this->person);
		}
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
			$tableName = $this->om->getClassMetadata(Person::class)->getTableName();

			$this->om->getConnection()->exec('UPDATE `' . $tableName . '` SET `person_type` = "person" WHERE `' . $tableName . '`.`id` = ' . strval(intval($this->person->getId())));

			$this->om->refresh($this->person);
		}
	}
}