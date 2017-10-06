<?php
namespace Busybee\People\PersonBundle\Model;

use Busybee\Core\SecurityBundle\Security\UserProvider;
use Busybee\Core\SystemBundle\Model\MessageManager;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\People\AddressBundle\Entity\Address;
use Busybee\People\LocalityBundle\Entity\Locality;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\PhoneBundle\Entity\Phone;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\ValidatorBuilder;

class ImportManager extends PersonManager
{
	/**
	 * @var string
	 */
	private $file;

	/**
	 * @var ArrayCollection
	 */
	private $fields;

	/**
	 * @var int
	 */
	private $offset;

	/**
	 * @var ValidatorBuilder
	 */
	private $validator;

	/**
	 * @var array
	 */
	private $tables;

	/**
	 * @var MessageManager
	 */
	private $results;

	/**
	 * @var Address|null
	 */
	private $address;

	/**
	 * @var Locality|null
	 */
	private $locality;

	/**
	 * @var int
	 */
	private $fieldCount;

	/**
	 * ImportManager constructor.
	 *
	 * @param ObjectManager  $om
	 * @param SettingManager $sm
	 * @param UserProvider   $userProvider
	 */
	public function __construct(ObjectManager $om, SettingManager $sm, UserProvider $userProvider, ValidatorBuilder $validator)
	{
		parent::__construct($om, $sm, $userProvider);
		$this->fields    = new ArrayCollection();
		$this->offset    = 0;
		$this->validator = $validator->getValidator();
		$this->results   = new MessageManager('BusybeePersonBundle');
	}

	/**
	 * Get Header Names
	 *
	 * @return ArrayCollection
	 */
	public function getHeaderNames(): ArrayCollection
	{
		$headerNames = new ArrayCollection();

		if (($handle = fopen($this->file, "r")) !== false)
		{
			if (($data = fgetcsv($handle)) !== false)
			{
				foreach ($data as $name)
					$headerNames->add($name);
			}
			fclose($handle);
		}

		$this->fieldCount = $headerNames->count();

		return $headerNames;
	}

	/**
	 * @param $import
	 *
	 * @return void
	 */
	public function importPeople($import)
	{
		$file          = $import['file'];
		$fields        = $import['fields'];

		$this->fields = [];
		foreach ($fields as $q => $w)
			if ($w['destination'] !== "")
				$this->fields[] = $w;
		$this->getHeaderNames();

		$destinationFields = $this->getFieldNames();

		$headers = false;
		$line    = 1;
		$offset  = empty($import['offset']) ? 0 : intval($import['offset']);

		if (($handle = fopen($file, "r")) !== false)
		{
			while (($data = fgetcsv($handle)) !== false)
			{
				if ($headers)
				{
					if ($line >= $offset)
					{
						ini_set('max_execution_time', '10');
						if (count($data) !== $this->fieldCount)
							$this->results->addMessage('danger', 'people.import.data.error', ['%data%' => $line]); // Return the offset to the form.
						else
							$this->importPerson($data, $this->fields, $destinationFields, ++$line);

						if ($line >= $offset + 200)
						{

							$this->results->addMessage('limit', 'people.import.limit.message', ['%data%' => $line]); // Return the offset to the form.
							$this->offset = $line;

							return;
						}
					}
					else
					{
						ini_set('max_execution_time', '10');
						$line++;
					}

				}
				else
				{
					$headers      = true;
					$this->tables = [];
					foreach ($this->fields as $q => $w)
					{
						$table = explode('.', $w['destination']);
						if (!in_array($table[0], $this->tables))
							$this->tables[] = $table[0];
					}
				}
			}
			fclose($handle);
		}
		$this->results->addMessage('info', 'people.import.complete.message', ['%data%' => $line - 1]);

		unlink($import['file']);

		return;
	}

	/**
	 * @return array
	 */
	public function getFieldNames()
	{
		$definition = $this->getOm()->getClassMetadata(Person::class);

		$result = $this->addFieldNames('person', $definition->getFieldNames());

		$definition = $this->getOm()->getClassMetadata(Student::class);

		$result = array_merge($result, $this->addFieldNames('student', $definition->getFieldNames()));

		$definition = $this->getOm()->getClassMetadata(Staff::class);

		$result = array_merge($result, $this->addFieldNames('staff', $definition->getFieldNames()));

		$definition = $this->getOm()->getClassMetadata(Phone::class);

		$result = array_merge($result, $this->addFieldNames('phone', $definition->getFieldNames()));

		$definition = $this->getOm()->getClassMetadata(Address::class);

		$result = array_merge($result, $this->addFieldNames('address', $definition->getFieldNames()));

		$definition = $this->getOm()->getClassMetadata(Locality::class);

		$result = array_merge($result, $this->addFieldNames('locality', $definition->getFieldNames()));


		$result = new ArrayCollection($result);

		$iterator = $result->getIterator();

		$iterator->uasort(function ($a, $b) {
			return ($a->field < $b->field) ? -1 : 1;
		});

		$result = iterator_to_array($iterator, false);

		$definition = $this->getOm()->getClassMetadata(Student::class);

		foreach ($definition->getFieldNames() as $field)
		{
			$p = null;
			$s = null;
			foreach ($result as $q => $w)
			{
				if ($w->table == 'student' && $w->field == $field)
					$s = $q;
				if ($w->table == 'person' && $w->field == $field)
					$p = $q;
				if (is_int($p) && is_int($s))
				{
					unset($result[$s]);
					break;
				}
			}
		}

		$definition = $this->getOm()->getClassMetadata(Staff::class);

		foreach ($definition->getFieldNames() as $field)
		{
			$p = null;
			$s = null;
			foreach ($result as $q => $w)
			{
				if ($w->table == 'staff' && $w->field == $field)
					$s = $q;
				if ($w->table == 'person' && $w->field == $field)
					$p = $q;
				if (is_int($p) && is_int($s))
				{
					unset($result[$s]);
					break;
				}
			}
		}

		$w = [];

		foreach ($result as $q)
			$w[$q->table . '.' . $q->field] = $q->field . ' (' . ucfirst($q->table) . ')';

		return $w;
	}

	/**
	 * @param $table
	 * @param $fields
	 *
	 * @return array
	 */
	public function addFieldNames($table, $fields)
	{
		$result = [];
		foreach ($fields as $field)
			if (!in_array($field, array('id', 'lastModified', 'createdOn')))
			{
				$w        = new \stdClass();
				$w->field = $field;
				$w->table = $table;
				$result[] = $w;
			}

		return $result;
	}

	/**
	 * @param $data
	 * @param $fields
	 * @param $destinationFields
	 * @param $line
	 *
	 * @return void
	 */
	private function importPerson($data, $fields, $destinationFields, $line)
	{
		$this->address  = null;
		$this->locality = null;

		if (!in_array('person', $this->tables))
		{
			$this->results->addMessage('warning', 'people.import.warning.nodata', ['%data%' => $line]);

			return;
		}

		$identifier = null;
		$idKey      = isset($destinationFields['person.importIdentifier']) ? 'person.importIdentifier' : false;
		if ($idKey !== false)
		{
			foreach ($fields as $q => $w)
				if ($w['destination'] == $idKey)
				{
					$identifier = $data[$w['source']];
					break;
				}

		}

		$person = new PersonProvider(null, $this->getOm());

		$person->createPersonbyImportIdentifier($identifier);


		foreach ($fields as $q => $w)
		{
			$table = explode('.', $w['destination']);
			$field = $table[1];
			$table = $table[0];
			if (in_array($table, ['person', 'student', 'staff']))
			{
				$person->setProperty($field, $data[$w['source']]);


				if ($field == 'dob' && !empty($data[$w['source']]) && strtoupper($data[$w['source']]) != 'NULL')
				{
					$dd = new \DateTime();

					$format = $w['option'] . ' H:i:s';
					$time   = $data[$w['source']] . ' 00:00:00';
					$dt     = $dd->createFromFormat($format, $time);

					if ($dt !== false && $dt->format($w['option']) == $data[$w['source']])
						$person->setProperty('dob', $dt);
				}
			}
		}

		$errors = $this->validator->validate($person);

		if ($errors->count() > 0)
		{
			$xx = '';
			foreach ($errors as $error)
				$xx .= '%newline%' . $error->getPropertyPath() . ': ' . $error->getMessage();
			$data[]            = $xx;
			$this->results->addMessage('warning', 'people.import.warning.invalid', ['%data%' => $line . ' ' . implode(', ', $data)]);
		}
		else
		{
			// Deal with the rest now
			$this->importOk = true;

			$person = $this->importAddress($data, $this->fields, $destinationFields, $person);
			$person = $this->importPhone($data, $this->fields, $destinationFields, $person);

			$errors = $this->validator->validate($person);

			if ($errors->count() > 0)
			{
				$this->importOk = false;
				$xx             = '';
				foreach ($errors as $error)
				{
					$xx .= '%newline%' . $error->getPropertyPath() . ': ' . $error->getMessage();
				}
				$data[]            = $xx;
				$this->results->addMessage('warning', 'people.import.warning.invalid', ['%data%' => $line . ' ' . implode(', ', $data)]);
			}
			if ($this->importOk)
			{
				$this->savePerson($person);
			}
			else
				$this->results->addMessage('warning', 'people.import.warning.person', ['%data%' => $line . ' ' . implode(', ', $data)]);
		}

		return;
	}

	/**
	 * @param        $data
	 * @param        $fields
	 * @param        $destinationFields
	 * @param Person $person
	 *
	 * @return Person
	 */
	private function importAddress($data, $fields, $destinationFields, PersonProvider $person)
	{
		$this->address   = null;
		$this->locality  = null;
		$this->addresses = array();

		if (!in_array('address', $this->tables)) return $person;

		$address = new Address();

		foreach ($fields as $q => $w)
		{
			if (mb_strpos($destinationFields[$w['destination']], 'address.') === 0)
			{
				$field  = str_replace('address.', '', $destinationFields[$w['destination']]);
				$method = 'set' . ucfirst($field);
				$address->$method($data[$w['source']]);
			}
		}

		if ($address->isEmpty())
			return $person;

		if (empty($this->address = $this->getOm()->getRepository(Address::class)->createQueryBuilder('a')
			->where('a.streetName = :streetName')
			->setParameter('streetName', $address->getStreetName())
			->getQuery()
			->getFirstResult()
		))
		{
			$this->importLocality($data, $fields, $destinationFields);

			if (is_null($this->locality))
			{
				$this->address     = null;
				$this->results->addMessage('warning', 'people.import.missing.locality', ['%name%' => $address->__toString()]);

				return $person;
			}

			$address->setLocality($this->locality);

			if (empty($address->getBuildingType()))
				$address->setBuildingType($this->getSm()->get('Person.Import.BuildingType'));
			if (empty($address->getBuildingNumber()))
				$address->setBuildingNumber($this->getSm()->get('Person.Import.BuildingNumber'));
			if (empty($address->getPropertyName()))
				$address->setPropertyName($this->getSm()->get('Person.Import.PropertyName'));
			if (empty($address->getStreetNumber()))
				$address->setStreetNumber($this->getSm()->get('Person.Import.StreetNumber'));
			if (empty($address->getStreetNumber()) && intval($address->getStreetName()) > 0)
			{
				$num = intval($address->getStreetName());
				$address->setStreetNumber(strval($num));
				$address->setStreetName(trim(str_replace($num, '', $address->getStreetName())));
			}

			$this->address = $this->getOm()->getRepository(Address::class)->createQueryBuilder('a')
				->where('a.buildingType = :buildingType')
				->andWhere('a.buildingNumber = :buildingNumber')
				->andWhere('a.streetNumber = :streetNumber')
				->andWhere('a.propertyName = :propertyName')
				->andWhere('a.streetName = :streetName')
				->andWhere('a.locality = :locality')
				->setParameter('buildingType', $address->getBuildingType())
				->setParameter('buildingNumber', $address->getBuildingNumber())
				->setParameter('streetNumber', $address->getStreetNumber())
				->setParameter('streetName', $address->getStreetName())
				->setParameter('propertyName', $address->getPropertyName())
				->setParameter('locality', intval($address->getLocality()->getId()))
				->getQuery()
				->getResult(1);

			if (!empty($this->address) && is_array($this->address))
				$this->address = reset($this->address);

			if (empty($this->address))
			{
				$this->address     = $address;
				$this->results->addMessage('success', 'people.import.success.address', ['%data%' => $address->__toString()]);
			}
			else
			{
				$address           = $this->address;
				$this->results->addMessage('success', 'people.import.duplicate.address', ['%data%' => $address->__toString()]);
			}
		}

		$person->set('address1', $address);

		return $person;
	}

	/**
	 * @param $data
	 * @param $fields
	 * @param $destinationFields
	 *
	 * @return void
	 */
	private function importLocality($data, $fields, $destinationFields)
	{
		$this->locality = null;

		$locality = new Locality();
		foreach ($fields as $q => $w)
		{
			if (mb_strpos($destinationFields[$w['destination']], 'locality.') === 0)
			{
				$field  = str_replace('locality.', '', $destinationFields[$w['destination']]);
				$method = 'set' . ucfirst($field);
				$locality->$method($data[$w['source']]);
			}
		}

		if ($locality->isEmpty())
			return;

		if (empty($locality->getPostCode() || empty($locality->getTerritory()) || empty($locality->getName())))
		{
			$this->results->addMessage('warning', 'people.import.warning.locality', ['%data%' => $locality->__toString()]);

			return;
		}

		if (!in_array($locality->getTerritory(), $this->getSm()->get('Address.TerritoryList')))
		{
			$this->results->addMessage('warning', 'people.import.warning.locality', ['%data%' => $locality->__toString()]);

			return;
		}

		if (empty($locality->getCountry()))
			$locality->setCountry($this->getSm()->get('Person.Import.CountryCode'));

		$this->locality = $this->getOm()->getRepository(Locality::class)->createQueryBuilder('l')
			->where('l.territory = :territory')
			->andWhere('l.name = :name')
			->andWhere('l.postCode = :postCode')
			->andWhere('l.country = :country')
			->setParameter('postCode', $locality->getPostCode())
			->setParameter('territory', $locality->getTerritory())
			->setParameter('name', $locality->getName())
			->setParameter('country', $locality->getCountry())
			->getQuery()
			->getResult(1);

		if (!empty($this->locality) && is_array($this->locality))
			$this->locality = reset($this->locality);

		if (empty($this->locality))
		{
			$this->locality    = $locality;
			$this->results->addMessage('success', 'people.import.success.locality', ['%data%' => $locality->__toString()]);
		}
		else
		{
			$locality          = $this->locality;
			$this->results->addMessage('success', 'people.import.duplicate.locality', ['%data%' => $locality->__toString()]);
		}

		return;
	}

	/**
	 * @param                $data
	 * @param                $fields
	 * @param                $destinationFields
	 * @param PersonProvider $person
	 *
	 * @return PersonProvider
	 */
	private function importPhone($data, $fields, $destinationFields, PersonProvider $person)
	{
		$this->phones = [];

		foreach ($fields as $q => $w)
		{
			if (mb_strpos($destinationFields[$w['destination']], 'phone.') === 0)
			{
				$phone  = new Phone();
				$type   = isset($w['option']) ? $w['option'] : 'Imported';
				$field  = str_replace('phone.', '', $destinationFields[$w['destination']]);
				$method = 'set' . ucfirst($field);
				$phone->$method(preg_replace('/\D/', '', $data[$w['source']]));
				$phone->setPhoneType($type);
				if (!array_key_exists(md5($phone->__toString()), $this->phones))
					$this->phones[md5($phone->__toString())] = $phone;
			}
		}

		foreach ($this->phones as $q => $phone)
			if (empty($existing = $this->getOm()->getRepository(Phone::class)->findOneByPhoneNumber($phone->getPhoneNumber())))
			{
				$phone->setCountryCode($this->countryCode);
				$this->results->addMessage('success', 'people.import.success.phone', ['%data%' => $phone->getPhoneNumber()]);
				$this->phones[$q]  = $phone;
			}
			else
			{
				$this->phones[$q] = $existing;
			}

		foreach ($this->phones as $phone)
		{
			$person->removePhone($phone);
			$person->addPhone($phone);
		}

		return $person;
	}

	/**
	 * @return mixed
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param $file
	 *
	 * @return $this
	 */
	public function setFile($file)
	{
		$this->file = $file;

		return $this;
	}

	/**
	 * @param FieldMatch $fieldMatch
	 *
	 * @return ImportManager
	 */
	public function addField(FieldMatch $fieldMatch): ImportManager
	{
		$this->fields->add($fieldMatch);

		return $this;
	}

	/**
	 * @param FieldMatch $fieldMatch
	 *
	 * @return ImportManager
	 */
	public function removeField(FieldMatch $fieldMatch): ImportManager
	{
		$this->fields->removeElement($fieldMatch);

		return $this;
	}

	/**
	 * @return ArrayCollection
	 */
	public function getFields(): ArrayCollection
	{
		return $this->fields;
	}

	/**
	 * @param ArrayCollection $fields
	 *
	 * @return ImportManager
	 */
	public function setFields(ArrayCollection $fields): ImportManager
	{
		$this->fields = $fields;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getOffset(): int
	{
		return $this->offset;
	}

	/**
	 * @param int $offset
	 */
	public function setOffset(int $offset): ImportManager
	{
		$this->offset = $offset;

		return $this;
	}

	public function getMessages(): array
	{
		return $this->results->getMessages();
	}

	public function savePerson(PersonProvider $person)
	{
		$person->getEntity();
		dump($person);

		if (!$person->wasStaff() && !$person->wasPerson() && !$person->wasStudent())
		{
			$this->getOm()->persist($person->getEntity());
			$this->getOm()->flush();
			if ($person->getEntity() instanceof Student)
			{
				$this->results->addMessage('success', 'people.import.success.student', ['%data%' => $person->formatName()]);

				return;
			}

			if ($person->getEntity() instanceof Staff)
			{
				$this->results->addMessage('success', 'people.import.success.staff', ['%data%' => $person->formatName()]);

				return;
			}
			$this->results->addMessage('success', 'people.import.success.person', ['%data%' => $person->formatName()]);

			return;
		}
		if ($person->nowStudent() && $person->wasStudent())
		{
			$this->getOm()->persist($person->getEntity());
			$this->getOm()->flush();
			$this->results->addMessage('success', 'people.import.success.student', ['%data%' => $person->formatName()]);

			return;
		}

		if ($person->nowStaff() && $person->wasStaff())
		{
			$this->getOm()->persist($person->getEntity());
			$this->getOm()->flush();
			$this->results->addMessage('success', 'people.import.success.staff', ['%data%' => $person->formatName()]);

			return;
		}

		if ($person->nowStudent() && $person->wasPerson())
		{
			if ($this->createStudent($person->getEntity(), true))
				$this->results->addMessage('success', 'people.import.change.student', ['%data%' => $person->formatName()]);
			else
				$this->results->addMessage('danger', 'people.import.error.student', ['%data%' => $person->formatName()]);

			return;
		}

		if ($person->nowStaff() && $person->wasPerson())
		{
			if ($this->createStaff($person->getEntity(), true))
				$this->results->addMessage('success', 'people.import.change.staff', ['%data%' => $person->formatName()]);
			else
				$this->results->addMessage('danger', 'people.import.error.staff', ['%data%' => $person->formatName()]);

			return;
		}

		if ($person->wasPerson())
		{
			$this->getOm()->persist($person->getEntity());
			$this->getOm()->flush();
			$this->results->addMessage('success', 'people.import.success.person', ['%data%' => $person->formatName()]);

			return;
		}

		if ($person->nowStaff() && $person->wasStudent())
		{
			$this->results->addMessage('warning', 'people.import.change.error.staff', ['%data%' => $person->formatName()]);

			return;
		}

		if ($person->nowStudent() && $person->wasStaff())
		{
			dump('8');
			$this->results->addMessage('warning', 'people.import.change.error.student', ['%data%' => $person->formatName()]);

			return;
		}


	}
}