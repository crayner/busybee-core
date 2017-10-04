<?php

namespace Busybee\People\PersonBundle\Model;

use Busybee\Core\SecurityBundle\Security\UserProvider;
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

		return $headerNames;
	}

	/**
	 * @param $import
	 *
	 * @return array
	 */
	public function importPeople($import)
	{
		$file          = $import['file'];
		$this->results = array();
		$fields        = $import['fields'];
		$this->fields  = array();
		foreach ($fields as $q => $w)
			if ($w['destination'] !== "")
				$this->fields[] = $w;

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
						$result = $this->importPerson($data, $this->fields, $destinationFields, ++$line);
						if (!empty($result)) $this->results[] = $result;

						if ($line >= $offset + 200)
						{

							$this->results[] = ['limit' => ['people.import.limit.message', $line]];  // Return the offset to the form.

							return $this->results;
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
					$this->tables = array();
					foreach ($this->fields as $q => $w)
					{
						$field = $destinationFields[$w['destination']];
						$table = explode('.', $field);
						if (!in_array($table[0], $this->tables))
							$this->tables[] = $table[0];
					}
				}
			}
			fclose($handle);
		}
		$this->results[] = ['info' => ['people.import.complete.message', --$line]];  // All done message.
		unlink($import['file']);

		return $this->results;
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
			dump($field);
			foreach ($result as $q => $w)
			{
				dump($w);
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
		$result = array();
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
	 * @return array
	 */
	private function importPerson($data, $fields, $destinationFields, $line)
	{
		$result         = array();
		$this->address  = null;
		$this->locality = null;

		if (!in_array('person', $this->tables))
		{
			$result['warning'] = ['people.import.warning.nodata', $line];

			return $result;
		}
		$idKey = array_search('person.identifier', $destinationFields);
		if ($idKey !== false)
		{
			foreach ($fields as $q => $w)
				if ($w['destination'] == $idKey)
				{
					$identifier = $data[$w['source']];
					break;
				}

		}
		if (empty($identifier))
			$person = new Person();
		else
		{
			$person = $this->getOm()->getRepository(Person::class)->findOneByIdentifier($identifier);
			$person = empty($person) ? new Person() : $person;
		}

		foreach ($fields as $q => $w)
		{
			if (mb_strpos($destinationFields[$w['destination']], 'person.') === 0)
			{
				$field  = str_replace('person.', '', $destinationFields[$w['destination']]);
				$method = 'set' . ucfirst($field);
				if (!empty($data[$w['source']]))
					$person->$method(strtoupper($data[$w['source']]) == 'NULL' ? null : trim($data[$w['source']]));
				if ($field == 'dob' && !empty($data[$w['source']]) && strtoupper($data[$w['source']]) != 'NULL')
				{
					$dd = new \DateTime();

					$dt = $dd->createFromFormat($w['option'] . ' H:i:s', $data[$w['source']] . ' 00:00:00');

					if ($dt->format($w['option']) == $data[$w['source']])
						$person->$method($dt);
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
			$result['warning'] = ['people.import.warning.invalid', $line . ' ' . implode(', ', $data)];
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
				$result['warning'] = ['people.import.warning.invalid', $line . ' ' . implode(', ', $data)];
			}
			if ($this->importOk)
			{
				$this->getOm()->persist($person);
				$this->getOm()->flush();
				$result['success'] = ['people.import.success.person', $line . ' ' . implode(', ', $data)];
			}
			else
				$result['warning'] = ['people.import.warning.person', $line . ' ' . implode(', ', $data)];
		}

		return $result;
	}

	/**
	 * @param        $data
	 * @param        $fields
	 * @param        $destinationFields
	 * @param Person $person
	 *
	 * @return Person
	 */
	private function importAddress($data, $fields, $destinationFields, Person $person)
	{
		$this->address   = null;
		$this->locality  = null;
		$result          = array();
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


		if (empty($this->address = $this->getOm()->getRepository(Address::class)->createQueryBuilder('l')
			->where('l.streetName = :streetName')
			->setParameter('streetName', $address->getStreetName())
			->getQuery()
			->getFirstResult()
		))
		{
			$this->results[] = $this->importLocality($data, $fields, $destinationFields);

			if (is_null($this->locality))
			{
				$this->address     = null;
				$result['warning'] = ['people.import.missing.locality', $address->__toString()];
				$this->results[]   = $result;

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
				$result['success'] = ['people.import.success.address', $address->__toString()];
			}
			else
			{
				$address           = $this->address;
				$result['success'] = ['people.import.duplicate.address', $address->__toString()];
			}
		}

		$person->setAddress1($address);

		$this->results[] = $result;

		return $person;
	}

	/**
	 * @param $data
	 * @param $fields
	 * @param $destinationFields
	 *
	 * @return array
	 */
	private function importLocality($data, $fields, $destinationFields)
	{
		$this->locality = null;

		$result = array();

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
		if (empty($locality->getPostCode() || empty($locality->getTerritory()) || empty($locality->getName())))
		{
			$result['warning'] = ['people.import.warning.locality', $locality->__toString()];

			return $result;
		}

		if (!in_array($locality->getTerritory(), $this->getSm()->get('Address.TerritoryList')))
		{
			$result['warning'] = ['people.import.warning.locality', $locality->__toString()];

			return $result;
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
			$result['success'] = ['people.import.success.locality', $locality->__toString()];
		}
		else
		{
			$locality          = $this->locality;
			$result['success'] = ['people.import.duplicate.locality', $locality->__toString()];
		}

		return $result;
	}

	/**
	 * @param        $data
	 * @param        $fields
	 * @param        $destinationFields
	 * @param Person $person
	 *
	 * @return Person
	 */
	private function importPhone($data, $fields, $destinationFields, Person $person)
	{
		$result       = array();
		$this->phones = array();

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
				$result['success'] = ['people.import.success.phone', $phone->getPhoneNumber()];
				$this->phones[$q]  = $phone;
			}
			else
			{
				$this->phones[$q] = $existing;
			}

		$this->results[] = $result;

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
}