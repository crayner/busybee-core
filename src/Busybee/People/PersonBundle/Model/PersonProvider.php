<?php

namespace Busybee\People\PersonBundle\Model;

use Busybee\Core\HomeBundle\Exception\Exception;
use Busybee\Core\SecurityBundle\Entity\User;
use Busybee\Core\SystemBundle\Model\MessageManager;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\Common\Persistence\ObjectManager;

class PersonProvider
{
	/**
	 * @var Person
	 */
	private $person;

	/**
	 * @var Student
	 */
	private $student;

	/**
	 * @var Staff
	 */
	private $staff;

	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * @var bool
	 */
	private $nowStudent = false;

	/**
	 * @var bool
	 */
	private $nowStaff = false;

	/**
	 * @var string
	 */
	private $personType = 'person';

	/**
	 * @var null|Person|Staff|Student
	 */
	private $entity = null;

	/**
	 * @var bool
	 */
	private $wasPerson = false;

	/**
	 * @var bool
	 */
	private $wasStaff = false;

	/**
	 * @var bool
	 */
	private $wasStudent = false;

	/**
	 * @var User
	 */
	private $user;

	/**
	 * @param $field
	 * @param $value
	 */
	public function setProperty($field, $value)
	{
		$value = strtoupper($value) == 'NULL' ? null : trim($value);

		if (empty($value))
			return;

		$method = 'set' . ucfirst($field);

		if ($field === 'importIdentifier')
			$value = ltrim($value, '0');

		if ($this->person instanceof Student && method_exists($this->student, $method))
		{
			$this->person->$method($value);
			$this->student->$method($value);
			if (!empty($value))
				$this->nowStudent = true;

			return;
		}
		elseif ($this->person instanceof Staff && method_exists($this->staff, $method))
		{
			$this->person->$method($value);
			$this->staff->$method($value);
			if (!empty($value))
				$this->nowStaff = true;

			return;
		}
		elseif ($this->person instanceof Person && method_exists($this->person, $method))
		{
			$this->person->$method($value);
			$this->student->$method($value);
			$this->staff->$method($value);

			return;
		}
	}

	/**
	 * @param $phone
	 *
	 * @return $this
	 */
	public function addPhone($phone)
	{
		$this->person->addPhone($phone);

		return $this;
	}

	/**
	 * @param $phone
	 *
	 * @return $this
	 */
	public function removePhone($phone)
	{
		$this->person->removePhone($phone);

		return $this;
	}

	/**
	 * set Person Type
	 *
	 * @param MessageManager|null $mm
	 *
	 * @throws Exception
	 * @return Person|Staff|Student
	 */
	public function setPersonType(MessageManager $mm = null)
	{
		if ($this->nowStudent && !$this->nowStaff)
		{
			$this->personType = 'Student';
			$this->entity     = $this->student;

			return $this->student;
		}
		if (!$this->nowStudent && $this->nowStaff)
		{
			$this->personType = 'Staff';
			$this->entity     = $this->staff;

			return $this->staff;
		}

		if (!$this->nowStudent && !$this->nowStaff)
		{
			$this->personType = 'Person';
			$this->entity     = $this->person;

			return $this->person;
		}
		if ($mm instanceof MessageManager)
			$mm->addMessage('danger', 'people.import.type.invalid', ['%name%' => $this->entity->formatName()]);
		else
			throw new Exception('The correct person type is not obtainable from this person. ' . $this->entity->formatName());

		$this->entity = null;

		return $this->person;
	}

	/**
	 * @param array $options
	 *
	 * @return string
	 */
	public function formatName($options = []): string
	{
		return $this->getEntity()->formatName($options);
	}

	/**
	 * @param MessageManager $mm
	 *
	 * @return Person|Staff|Student
	 */
	public function getEntity(MessageManager $mm = null)
	{
		if (is_null($this->entity))
			return $this->setPersonType($mm);

		return $this->entity;
	}

	/**
	 * @param $identifier
	 *
	 * @return PersonProvider
	 */
	public function createPersonbyImportIdentifier($identifier)
	{
		if (empty($identifier))
			return $this->__construct(new Person(), $this->om);

		$identifier = ltrim($identifier, '0');

		$this->student = $this->om->getRepository(Student::class)->findOneByImportIdentifier($identifier);

		if ($this->student instanceof Student)
			return $this->createFromStudent();


		$this->staff = $this->om->getRepository(Staff::class)->findOneByImportIdentifier($identifier);

		if ($this->staff instanceof Staff)
			return $this->createFromStaff();

		$this->person = $this->om->getRepository(Person::class)->findOneByImportIdentifier($identifier);

		if ($this->person instanceof Person)
			return $this->createFromPerson();

	}

	/**
	 * PersonProvider constructor.
	 *
	 * @param Person        $person
	 * @param ObjectManager $om
	 * @param User          $user
	 */
	public function __construct(Person $person = null, ObjectManager $om, User $user)
	{
		$this->om   = $om;
		$this->user = $user;

		if (is_null($person))
			return;

		$this->person = $person;

		$this->staff   = $this->om->getRepository(Staff::class)->find($this->person->getId());
		$this->student = $this->om->getRepository(Student::class)->find($this->person->getId());

		if (!$this->staff instanceof Staff)
			$this->staff = new Staff();
		else
			$this->nowStaff = true;

		if (!$this->student instanceof Student)
			$this->student = new Student();
		else
			$this->nowStudent = true;
	}

	/**
	 *
	 */
	private function createFromPerson()
	{
		$this->student   = new Student();
		$this->staff     = new Staff();
		$this->wasPerson = true;

		$this->copyFields($this->person, $this->staff, $this->student);
	}

	/**
	 * @param Object $source
	 * @param Object $dest1
	 * @param Object $dest2
	 */
	private function copyFields($source, $dest1, $dest2)
	{
		$entity = $this->om->getClassMetadata(Person::class);
		$fields = $entity->getFieldNames();

		foreach ($fields as $field)
		{
			$set = 'set' . ucfirst($field);
			$get = 'get' . ucfirst($field);
			$dest1->$set($source->$get());
			$dest2->$set($source->$get());
		}
	}

	/**
	 *
	 */
	private function createFromStudent()
	{
		$this->person     = new Person();
		$this->staff      = new Staff();
		$this->wasStudent = true;

		$this->copyFields($this->student, $this->person, $this->staff);
	}

	/**
	 *
	 */
	private function createFromStaff()
	{
		$this->student  = new Student();
		$this->person   = new Person();
		$this->wasStaff = true;

		$this->copyFields($this->staff, $this->student, $this->person);
	}

	/**
	 * @return bool
	 */
	public function wasStaff(): bool
	{
		return $this->wasStaff;
	}

	/**
	 * @return bool
	 */
	public function wasStudent(): bool
	{
		return $this->wasStudent;
	}

	/**
	 * @return bool
	 */
	public function wasPerson(): bool
	{
		return $this->wasPerson;
	}

	/**
	 * @return bool
	 */
	public function nowStaff(): bool
	{
		return $this->nowStaff;
	}

	/**
	 * @return bool
	 */
	public function nowStudent(): bool
	{
		return $this->nowStudent;
	}

	/**
	 * @param MessageManager $mm
	 */
	public function switchToStudent(MessageManager $mm)
	{
		$tableName = $this->om->getClassMetadata(Person::class)->getTableName();

		$id = strval(intval($this->person->getId()));

		$entity  = $this->om->getClassMetadata(Student::class);
		$fields  = $entity->getFieldNames();

		$data = [];

		foreach ($fields as $field)
		{
			$get = 'get' . ucfirst($field);

			if (!empty($this->student->$get()) && $field !== 'id')
				$data[$entity->getColumnName($field)] = $this->student->$get();
		}

		$data['last_modified'] = new \DateTime();
		$data['modified_by']   = $this->user->getId();
		$data['person_type']   = 'student';
		$query                 = 'UPDATE `' . $tableName . '` SET ';
		foreach ($data as $col => $value)
		{
			if ($value instanceof \DateTime)
				$query .= '`' . $tableName . '`.`' . $col . '` = "' . $value->format('Y-m-d H:i:s') . '", ';
			else
				$query .= '`' . $tableName . '`.`' . $col . '` = "' . $value . '", ';
		}
		$query = rtrim($query, ', ');
		$query .= ' WHERE `' . $tableName . '`.`id` = ' . $id;

		$this->om->getConnection()->exec($query);

		if ($this->wasStudent())
			$mm->addMessage('success', 'people.import.success.student', ['%data%' => $this->student->formatName()]);
		else
			$mm->addMessage('success', 'people.import.change.student', ['%data%' => $this->student->formatName()]);

		$this->personType = 'student';
		$this->nowStudent = true;
	}

	/**
	 * @param MessageManager $mm
	 */
	public function switchToStaff(MessageManager $mm)
	{
		$tableName = $this->om->getClassMetadata(Person::class)->getTableName();

		$id = strval(intval($this->person->getId()));

		$entity = $this->om->getClassMetadata(Staff::class);
		$fields = $entity->getFieldNames();

		$data = [];

		foreach ($fields as $field)
		{
			$get = 'get' . ucfirst($field);

			if (!empty($this->staff->$get()) && $field !== 'id')
				$data[$entity->getColumnName($field)] = $this->staff->$get();
		}

		$data['last_modified'] = new \DateTime();
		$data['modified_by']   = $this->user->getId();
		$data['person_type']   = 'staff';
		$query                 = 'UPDATE `' . $tableName . '` SET ';
		foreach ($data as $col => $value)
		{
			if ($value instanceof \DateTime)
				$query .= '`' . $tableName . '`.`' . $col . '` = "' . $value->format('Y-m-d H:i:s') . '", ';
			else
				$query .= '`' . $tableName . '`.`' . $col . '` = "' . $value . '", ';
		}
		$query = rtrim($query, ', ');
		$query .= ' WHERE `' . $tableName . '`.`id` = ' . $id;

		$this->om->getConnection()->exec($query);

		if ($this->wasStaff())
			$mm->addMessage('success', 'people.import.success.staff', ['%data%' => $this->staff->formatName()]);
		else
			$mm->addMessage('success', 'people.import.change.staff', ['%data%' => $this->staff->formatName()]);

		$this->personType = 'staff';
		$this->nowStaff   = true;
	}

	/**
	 * @return string
	 */
	public function getPersonType(): string
	{
		return $this->personType;
	}

}