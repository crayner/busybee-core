<?php
namespace Busybee\PersonBundle\Model;

use Busybee\FamilyBundle\Entity\Family;
use Busybee\InstituteBundle\Entity\CampusResource;
use Busybee\PersonBundle\Entity\Address;
use Busybee\PersonBundle\Entity\CareGiver;
use Busybee\PersonBundle\Entity\Locality;
use Busybee\PersonBundle\Entity\Person;
use Busybee\PersonBundle\Entity\Phone;
use Busybee\PersonBundle\Entity\Staff;
use Busybee\PersonBundle\Entity\Student;
use Busybee\SecurityBundle\Entity\User;
use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager ;
use Symfony\Component\Validator\Validator\ValidatorInterface ;

class PersonManager
{
    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityManager
     */
    private $validator;

    /**
     * @var array
     */
    private $addresses;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $phones;

    /**
     * @var array
     */
    private $results;

    /**
     * @var array
     */
    private $localities;

    /**
     * @var Locality
     */
    private $locality;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var boolean
     */
    private $importOk;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * PersonManager constructor.
     *
     * @param SettingManager $sm
     * @return void
     */
    public function __construct(SettingManager $sm, EntityManager $em, ValidatorInterface $validator)
    {
        ini_set('auto_detect_line_endings', true);

        $this->sm = $sm;
        $this->em = $em;
        $this->validator = $validator ;
        $this->countryCode = $this->sm->get('Country.Code');
    }

    /**
     * @return array
     */
    public function getTitles()
    {
        return self::$sm->get('Person.TitleList');
    }

    /**
     * @return array
     */
    public function getGenders()
    {
        return self::$sm->get('Person.GenderList');
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeStudent(Person $person)
    {
        //plcae rules here to stop new student.
        if ($this->isCareGiver($person) || $this->isStaff($person))
            return false;
        return true;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isCareGiver(Person $person)
    {
        $carer = $this->em->getRepository(CareGiver::class)->findOneByPerson($person->getId());
        if ($carer instanceof CareGiver)
            return true;
        return false;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isStaff(Person $person)
    {
        $staff = $this->em->getRepository(Staff::class)->findOneByPerson($person->getId());
        if ($staff instanceof Staff)
            return true;
        return false;
    }

    /**
     * @param Person $person
     * @param $parameters
     */
    public function deleteStudent(Person $person, $parameters)
    {
        if ($this->canDeleteStudent($person, $parameters)) {
            $student = $person->getStudent();
            $families = $student->getFamilies($this->em->getRepository(Family::class));
            if (count($families) > 0)
                foreach ($families as $family)
                    if ($family instanceof Family) {
                        $family->removeStudent($student);
                        $this->em->persist($family);
                    }

            if (is_array($parameters) && $student instanceof Student)
                foreach ($parameters as $data)
                    if (isset($data['data']['name']) && isset($data['entity']['name'])) {
                        $client = $this->em->getRepository($data['entity']['name'])->findOneByStudent($student->getId());
                        if (is_object($client))
                            $this->em->remove($client);
                    }
            $person->setStudent(null);
            $this->em->remove($student);
            $this->em->persist($person);
            $this->em->flush();
        }
    }

    /**
     * @param Person $person
     * @param array $parameters
     * @return bool
     */
    public function canDeleteStudent(Person $person, $parameters)
    {
        //Place rules here to stop delete .
        $student = $this->em->getRepository(Student::class)->findOneByPerson($person->getId());

        $families = $student->getFamilies($this->em->getRepository(Family::class));
        if (is_array($families) && count($families) > 0)
            return false;

        if (is_array($parameters))
            foreach ($parameters as $data)
                if (isset($data['data']['name']) && isset($data['entity']['name'])) {
                    $client = $this->em->getRepository($data['entity']['name'])->findOneByStudent($student->getId());

                    if (is_object($client) && $client->getId() > 0)
                        return false;
                }
        return $student->canDelete();
    }

    /**
     * @param Person $person
     */
    public function deleteCareGiver(Person $person)
    {
        if ($this->canDeleteCareGiver($person)) {
            $careGiver = $this->em->getRepository(CareGiver::class)->findOneByPerson($person->getId());
            $this->em->remove($careGiver);
            $this->em->flush();
        }
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canDeleteCareGiver(Person $person)
    {
        //Place rules here to stop delete .
        $careGiver = $this->em->getRepository(CareGiver::class)->findOneByPerson($person->getId());

        if (is_null($careGiver))
            return false;

        $families = $careGiver->getFamilies($this->em->getRepository(Family::class));
        if (is_array($families) && count($families) > 0)
            return false;
        if (null !== $this->em->getRepository(Family::class)->findOneByCareGiver1($careGiver->getId())) return false;
        if (null !== $this->em->getRepository(Family::class)->findOneByCareGiver2($careGiver->getId())) return false;

        return $careGiver->canDelete();
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeCareGiver(Person $person)
    {
        //plcae rules here to stop new student.
        if ($this->isStudent($person))
            return false;
        return true;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isStudent(Person $person)
    {
        $student = $this->em->getRepository(Student::class)->findOneByPerson($person->getId());
        if ($student instanceof Student)
            return true;
        return false;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeStaff(Person $person)
    {
        //plcae rules here to stop new student.
        if ($this->isStudent($person))
            return false;
        return true;
    }

    /**
     * @param Person $person
     */
    public function deleteStaff(Person $person)
    {
        if ($this->canDeleteStaff($person)) {
            $staff = $this->em->getRepository(Staff::class)->findOneByPerson($person->getId());
            $this->em->remove($staff);
            $this->em->flush();
        }
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canDeleteStaff(Person $person)
    {
        //Place rules here to stop delete .
        $staff = $this->em->getRepository(Staff::class)->findOneByPerson($person->getId());

        if (is_null($staff))
            return false;

        if (null !== $this->em->getRepository(CampusResource::class)->findOneByStaff1($staff->getId())) return false;
        if (null !== $this->em->getRepository(CampusResource::class)->findOneByStaff2($staff->getId())) return false;

        return $staff->canDelete();
    }

    /**
     * @return SettingManager
     */
    public function getSettingManager()
    {
        return $this->sm;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeUser(Person $person)
    {
        if (empty($person->getEmail()))
            return false;
        return true;
    }

    /**
     * @param Person $person
     */
    public function deleteUser(Person $person)
    {
        if ($this->canDeleteUser($person)) {
            $user = $this->em->getRepository(User::class)->findOneByPerson($person->getId());
            $this->em->remove($user);
            $this->em->flush();
        }
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canDeleteUser(Person $person)
    {
        //Place rules here to stop delete .
        $user = $this->em->getRepository(User::class)->findOneByPerson($person->getId());
        if (!$user instanceof User)
            return false;

        return $user->canDelete();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->sm->getParameter($name);
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isUser(Person $person)
    {
        $user = $this->em->getRepository(User::class)->findOneByPerson($person->getId());
        if ($user instanceof User)
            return true;
        return false;
    }

    /**
     * @param $import
     * @return array
     */
    public function importPeople($import)
    {
        $file = $import['file'];
        $this->results = array();
        $fields = $import['fields'];
        $this->fields = array();
        foreach ($fields as $q => $w)
            if ($w['destination'] !== "")
                $this->fields[] = $w;

        $destinationFields = $this->getFieldNames();

        // Handle Localities
        $headers = false;
        $line = 1;
        if (($handle = fopen($file, "r")) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                if ($headers) {
                    ini_set('max_execution_time', '30');
                    $this->results = array_merge($this->results, $this->importPerson($data, $this->fields, $destinationFields, ++$line));
                } else {
                    $headers = true;
                    $this->tables = array();
                    foreach ($this->fields as $q => $w) {
                        $field = $destinationFields[$w['destination']];
                        $table = explode('.', $field);
                        if (!in_array($table[0], $this->tables))
                            $this->tables[] = $table[0];
                    }
                }
            }
            fclose($handle);
        }

        return $this->results;
    }

    /**
     * @return array
     */
    public function getFieldNames()
    {
        $definition = $this->em->getClassMetadata(Person::class);

        $result = $this->addFieldNames('person', $definition->getFieldNames());

        $definition = $this->em->getClassMetadata(Phone::class);

        $result = array_merge($result, $this->addFieldNames('phone', $definition->getFieldNames()));

        $definition = $this->em->getClassMetadata(Address::class);

        $result = array_merge($result, $this->addFieldNames('address', $definition->getFieldNames()));

        $definition = $this->em->getClassMetadata(Locality::class);

        $result = array_merge($result, $this->addFieldNames('locality', $definition->getFieldNames()));

        return $result;
    }

    /**
     * @param $table
     * @param $fields
     * @return array
     */
    public function addFieldNames($table, $fields)
    {
        $result = array();
        foreach ($fields as $field)
            if (!in_array($field, array('id', 'lastModified', 'createdOn')))
                $result[] = $table . '.' . $field;
        return $result;
    }

    /**
     * @param $data
     * @param $fields
     * @param $destinationFields
     */
    private function importPerson($data, $fields, $destinationFields, $line)
    {
        $result = array();
        $this->address = null;
        $this->locality = null ;

        if (!in_array('person', $this->tables)) {
            $result['warning'] = ['people.import.warning.nodata', $line];
            return $result;
        }
        $idKey =  array_search('person.identifier', $destinationFields);
        if ($idKey !== false)
        {
            foreach($fields as $q=>$w)
                if ($w['destination'] == $idKey)
                {
                    $identifier = $data[$w['source']];
                    break ;
                }

        }
        if (empty($identifier))
            $person = new Person();
        else {
            $person = $this->em->getRepository(Person::class)->findOneByIdentifier($identifier);
            $person = empty($person) ? new Person() : $person ;
        }

        foreach ($fields as $q => $w) {
            if (mb_strpos($destinationFields[$w['destination']], 'person.') === 0) {
                $field = str_replace('person.', '', $destinationFields[$w['destination']]);
                $method = 'set' . ucfirst($field);
                if (! empty($data[$w['source']]))
                    $person->$method($data[$w['source']]);
            }
        }

        if (empty($person->getPreferredName()))
            $person->setPreferredName($person->getFirstName());

        $errors = $this->validator->validate($person);
        if ($errors->count() > 0) {
            foreach($errors as $error)
                $data[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            $result['warning'] = ['people.import.warning.invalid', implode(', ', $data)];
        } else {
            // Deal with the rest now
            $this->importOk = true;

            $person = $this->importAddress($data, $this->fields, $destinationFields, $person);
            $person = $this->importPhone($data, $this->fields, $destinationFields, $person);
            $errors = $this->validator->validate($person);
            if ($errors->count() > 0){
                $this->importOk = false;
                foreach($errors as $error)
                    $data[] = $error->getPropertyPath() . ': ' . $error->getMessage();
                $result['warning'] = ['people.import.warning.invalid', implode(', ', $data)];
            }
            if ($this->importOk) {
                $this->em->persist($person);
                $this->em->flush();
                $result['success'] = ['people.import.success.person', $line . ' ' . implode(', ', $data)];
            } else
                $result['warning'] = ['people.import.warning.person', $line . ' ' . implode(', ', $data)];
        }
        return $result ;
    }

    /**
     * @param $data
     * @param $fields
     * @param $destinationFields
     * @param Person $person
     * @return Person
     */
    private function importAddress($data, $fields, $destinationFields, Person $person)
    {
        $this->address = null;
        $result = array();
        $this->localities = array();
        $this->addresses = array();

        $address = new Address();

        foreach ($fields as $q => $w) {
            if (mb_strpos($destinationFields[$w['destination']], 'address.') === 0) {
                $field = str_replace('address.', '', $destinationFields[$w['destination']]);
                $method = 'set' . ucfirst($field);
                $address->$method($data[$w['source']]);
            }
        }


        if (empty($this->address = $this->em->getRepository(Address::class)->createQueryBuilder('l')
            ->where('l.streetName = :streetName')
            ->setParameter('streetName', $address->getStreetName())
            ->getQuery()
            ->getFirstResult()
        ))
        {
            $this->results[] = $this->importLocality($data, $fields, $destinationFields);

            $address->setLocality($this->locality);

            if (empty($address->getBuildingType()))
                $address->setBuildingType($this->sm->get('Person.Import.BuildingType'));
            if (empty($address->getBuildingNumber()))
                $address->setBuildingNumber($this->sm->get('Person.Import.BuildingNumber'));
            if (empty($address->getPropertyName()))
                $address->setPropertyName($this->sm->get('Person.Import.PropertyName'));
            if (empty($address->getStreetNumber()))
                $address->setStreetNumber($this->sm->get('Person.Import.StreetNumber'));
            if (empty($address->getStreetNumber()) && intval($address->getStreetName()) > 0)
            {
                $num = intval($address->getStreetName());
                $address->setStreetNumber(strval($num));
                $address->setStreetName(trim(str_replace($num, '', $address->getStreetName())));
            }

            $this->address = $this->em->getRepository(Address::class)->createQueryBuilder('a')
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

            if (empty($this->address)) {
                if (! array_key_exists(md5($address->__toString()), $this->addresses)) {
                    $this->address = $this->addresses[md5($address->__toString())] = $address ;
                } else {
                    $address = $this->address = $this->addresses[md5($address->__toString())];
                    $result['success'] = ['people.import.duplicate.address', $address->__toString()];
                }

                $result['success'] = ['people.import.success.address', $address->__toString()];
            } elseif (is_array($this->address) && ! empty($this->address))
            {
                $address = reset($this->address);
                $this->address = $this->addresses[md5($address->__toString())] = $address ;
                $result['success'] = ['people.import.duplicate.address', $address->__toString()];
            }
        }
        $person->setAddress1($address);

        $this->results[] = $result ;

        return $person;
    }

    /**
     * @param $data
     * @param $fields
     * @param $destinationFields
     * @return array
     */
    private function importLocality($data, $fields, $destinationFields)
    {
        $this->locality = null;

        $result = array();

        $locality = new Locality();

        foreach ($fields as $q => $w) {
            if (mb_strpos($destinationFields[$w['destination']], 'locality.') === 0) {
                $field = str_replace('locality.', '', $destinationFields[$w['destination']]);
                $method = 'set' . ucfirst($field);
                $locality->$method($data[$w['source']]);
            }
        }

        if (empty($locality->getPostCode() || empty($locality->getTerritory()) || empty($locality->getName()))) {
            $result['warning'] = ['people.import.warning.locality', $locality->__toString()];
            return $result;
        }
        if (!in_array($locality->getTerritory(), $this->sm->get('Address.TerritoryList'))) {
            $result['warning'] = ['people.import.warning.locality', $locality->__toString()];
            return $result;
        }

        if (empty($locality->getCountry()))
            $locality->setCountry($this->sm->get('Person.Import.CountryCode'));

        $this->locality = $this->em->getRepository(Locality::class)->createQueryBuilder('l')
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

        if (empty($this->locality)) {
            if (! array_key_exists(md5($locality->__toString()), $this->localities)) {
                $this->em->persist($locality);
                $this->locality = $this->localities[md5($locality->__toString())] = $locality ;
            } else {
                $locality = $this->locality = $this->localities[md5($locality->__toString())];
                $result['warning'] = ['people.import.duplicate.locality', $locality->__toString()];
            }
            $result['success'] = ['people.import.success.locality', $locality->__toString()];
        } elseif (is_array($this->locality) && ! empty($this->locality))
        {
            $locality = reset($this->locality);
            $this->locality = $this->localities[md5($locality->__toString())] = $locality ;
            $result['success'] = ['people.import.duplicate.locality', $locality->__toString()];
        }

        return $result;
    }

    /**
     * @param $data
     * @param $fields
     * @param $destinationFields
     * @param Person $person
     * @return Person
     */
    private function importPhone($data, $fields, $destinationFields, Person $person)
    {
        $result = array();
        $this->phones = array();

        foreach ($fields as $q => $w) {
            if (mb_strpos($destinationFields[$w['destination']], 'phone.') === 0) {
                $phone = new Phone();
                $type = isset($w['option']) ? $w['option'] : 'Imported';
                $field = str_replace('phone.', '', $destinationFields[$w['destination']]);
                $method = 'set' . ucfirst($field);
                $phone->$method(preg_replace('/\D/', '', $data[$w['source']]));
                $phone->setPhoneType($type);
                if (! array_key_exists(md5($phone->__toString()), $this->phones))
                    $this->phones[md5($phone->__toString())] = $phone;
            }
        }

        foreach ($this->phones as $q=>$phone)
            if (empty($existing = $this->em->getRepository(Phone::class)->findOneByPhoneNumber($phone->getPhoneNumber()))) {
                $phone->setCountryCode($this->countryCode);
                $result['success'] = ['people.import.success.phone', $phone->getPhoneNumber()];
                $this->phones[$q] = $phone;
            } else {
                $this->phones[$q] = $existing;
            }

        $this->results[] = $result;

        foreach($this->phones as $phone) {
            $person->removePhone($phone);
            $person->addPhone($phone);
        }

        return $person;
    }

    /**
     * @param $file
     * @return ArrayCollection
     */
    public function getHeaderNames($file)
    {
        $headerNames = new ArrayCollection();

        if (($handle = fopen($file, "r")) !== false) {
            if (($data = fgetcsv($handle)) !== false) {
                foreach ($data as $name)
                    $headerNames->add($name);
            }
            fclose($handle);
        }

        return $headerNames;
    }

    /**
     * @param Person $person
     */
    public function doesThisUserExist(Person $person)
    {
        $user = $this->em->getRepository(User::class)->findOneByEmail($person->getEmail());
        return $user ;
    }
}