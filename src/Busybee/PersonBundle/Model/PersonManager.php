<?php
namespace Busybee\PersonBundle\Model;

use Busybee\InstituteBundle\Entity\CampusResource;
use Busybee\PersonBundle\Entity\Address;
use Busybee\PersonBundle\Entity\Locality;
use Busybee\PersonBundle\Entity\Person;
use Busybee\PersonBundle\Entity\Phone;
use Busybee\SecurityBundle\Entity\User;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\StudentBundle\Entity\Student;
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
     * @var Locality
     */
    private $locality;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var array
     */
    private $tables;

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
     */
    public function deleteStudent(Person $person, $parameters)
    {
        if ($this->canDeleteStudent($person, $parameters)) {
            $student = $person->getStudent();
            /*            $families = $student->getFamilies($this->em->getRepository(Family::class));
                        if (count($families) > 0)
                            foreach ($families as $family)
                                if ($family instanceof Family) {
                                    $family->removeStudent($student);
                                    $this->em->persist($family);
                                } */
            if (is_array($parameters) && $student instanceof Student)
                foreach ($parameters as $data)
                    if (isset($data['data']['name']) && isset($data['entity']['name'])) {
                        $client = $this->em->getRepository($data['entity']['name'])->findOneByStudent($student->getId());
                        if (is_object($client))
                            $this->em->remove($client);
                    }
            $person->setStudent(null);
            $person->setStudentQuestion(false);
            if ($student instanceof Student)
                $this->em->remove($student);
            $this->em->persist($person);
            $this->em->flush();
        }
    }

    /**
     * @param   Person $person
     * @param   array $parameters
     * @return  bool
     */
    public function canDeleteStudent(Person $person, $parameters)
    {
        return true;
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
     * @param   Person $person
     * @return  bool
     */
    public function isStudent(Person $person)
    {
        return $person->getStudentQuestion();
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function deleteStaff(Person $person)
    {
        if ($this->canDeleteStaff($person)) {
            $staff = $this->em->getRepository(Staff::class)->findOneByPerson($person->getId());
            if ($staff instanceof Staff)
                $this->em->remove($staff);
            $person->setStaffQuestion(false);
            $person->setStaff(null);
            $this->em->persist($person);
            $this->em->flush();
            return true;
        }
        return false;
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
            return true;
        /*
                if (null !== $this->em->getRepository(CampusResource::class)->findOneByStaff1($staff->getId())) return false;
                if (null !== $this->em->getRepository(CampusResource::class)->findOneByStaff2($staff->getId())) return false;
        */
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

        $headers = false;
        $line = 1;
        $offset = empty($import['offset']) ? 0 : intval($import['offset']);

        if (($handle = fopen($file, "r")) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                if ($headers) {
                    if ($line >= $offset) {
                        ini_set('max_execution_time', '10');
                        $result =  $this->importPerson($data, $this->fields, $destinationFields, ++$line);
                        if (! empty($result))  $this->results[] = $result ;

                        if ($line >= $offset + 200) {

                            $this->results[] = ['limit' => ['people.import.limit.message', $line]];  // Return the offset to the form.
                            return $this->results;
                        }
                    } else {
                        ini_set('max_execution_time', '10');
                        $line++;
                    }

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
        $this->results[] = ['info' => ['people.import.complete.message', --$line]];  // All done message.
        unlink($import['file']);
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

        if (! in_array('person', $this->tables)) {
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
                    $person->$method(strtoupper($data[$w['source']]) == 'NULL' ? null : trim($data[$w['source']]));
                if ($field == 'dob' && !empty($data[$w['source']]) && strtoupper($data[$w['source']]) != 'NULL') {
                    $dd = new \DateTime();

                    $dt = $dd->createFromFormat($w['option'] . ' H:i:s', $data[$w['source']] . ' 00:00:00');

                    if ($dt->format($w['option']) == $data[$w['source']])
                        $person->$method($dt);
                }
            }
        }


        $errors = $this->validator->validate($person);

        if ($errors->count() > 0) {
            $xx = '';
            foreach($errors as $error)
                $xx .= '%newline%' . $error->getPropertyPath() . ': ' . $error->getMessage();
            $data[] = $xx;
            $result['warning'] = ['people.import.warning.invalid', $line . ' ' .implode(', ', $data)];
        } else {
            // Deal with the rest now
            $this->importOk = true;

            $person = $this->importAddress($data, $this->fields, $destinationFields, $person);
            $person = $this->importPhone($data, $this->fields, $destinationFields, $person);

            $errors = $this->validator->validate($person);

            if ($errors->count() > 0){
                $this->importOk = false;
                $xx = '';
                foreach($errors as $error) {
                    $xx .= '%newline%' . $error->getPropertyPath() . ': ' . $error->getMessage();
                }
                $data[] = $xx;
                $result['warning'] = ['people.import.warning.invalid', $line . ' ' . implode(', ', $data)];
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
        $this->locality = null;
        $result = array();
        $this->addresses = array();

        if (! in_array('address', $this->tables)) return $person;

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
        )) {
            $this->results[] = $this->importLocality($data, $fields, $destinationFields);

            if (is_null($this->locality)) {
                $this->address = null;
                $result['warning'] = ['people.import.missing.locality', $address->__toString()];
                $this->results[] = $result;
                return $person;
            }

            $address->setLocality($this->locality);

            if (empty($address->getBuildingType()))
                $address->setBuildingType($this->sm->get('Person.Import.BuildingType'));
            if (empty($address->getBuildingNumber()))
                $address->setBuildingNumber($this->sm->get('Person.Import.BuildingNumber'));
            if (empty($address->getPropertyName()))
                $address->setPropertyName($this->sm->get('Person.Import.PropertyName'));
            if (empty($address->getStreetNumber()))
                $address->setStreetNumber($this->sm->get('Person.Import.StreetNumber'));
            if (empty($address->getStreetNumber()) && intval($address->getStreetName()) > 0) {
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

            if (!empty($this->address) && is_array($this->address))
                $this->address = reset($this->address);

            if (empty($this->address)) {
                $this->address = $address;
                $result['success'] = ['people.import.success.address', $address->__toString()];
            } else {
                $address = $this->address;
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

        if (! in_array($locality->getTerritory(), $this->sm->get('Address.TerritoryList'))) {
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

        if (! empty($this->locality) && is_array($this->locality))
            $this->locality = reset($this->locality);

        if (empty($this->locality)) {
            $this->locality = $locality ;
            $result['success'] = ['people.import.success.locality', $locality->__toString()];
        } else {
            $locality = $this->locality ;
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

    /**
     * @param Person $person
     * @return ArrayCollection
     */
    public function getAddresses(Person $person)
    {
        $families = $this->getFamilies($person);
        $addresses = new ArrayCollection();
        foreach($families as $family)
        {
            $address = $family->getAddress1();
            if (! is_null($address) && ! $addresses->contains($address))
                $addresses->add($address);
            $address = $family->getAddress2();
            if (! is_null($address) && ! $addresses->contains($address))
                $addresses->add($address);
        }

        return $addresses;
    }

    /**
     * @param Person $person
     * @return ArrayCollection
     */
    public function getFamilies(Person $person)
    {
        $families = new ArrayCollection();
        /*
        $careGiver = $this->em->getRepository(CareGiver::class)->findOneByPerson($person);
        if (!is_null($careGiver)) {
            $xx = $this->em->getRepository(Family::class)->createQueryBuilder('f')
                ->where('f.careGiver1 = :careGiver')
                ->orWhere('f.careGiver2 = :careGiver')
                ->setParameter('careGiver', $careGiver->getId())
                ->getQuery()
                ->getResult();
            if (!empty($xx) && is_array($xx))
                foreach ($xx as $family)
                    if (!$families->contains($family))
                        $families->add($family);
            $xx = $this->em->getRepository(Family::class)->createQueryBuilder('f')
                ->leftJoin('f.emergencyContact', 'c')
                ->where('c.id = :careGiverID')
                ->setParameter('careGiverID', $careGiver->getId())
                ->getQuery()
                ->getResult();
            if (!empty($xx) && is_array($xx))
                foreach ($xx as $family)
                    if (!$families->contains($family))
                        $families->add($family);
        }
        $student = $this->em->getRepository(Student::class)->findOneByPerson($person);
        if (!is_null($student)) {
            $xx = $this->em->getRepository(Family::class)->createQueryBuilder('f')
                ->leftJoin('f.students', 's')
                ->where('s.id = :studentID')
                ->setParameter('studentID', $student->getId())
                ->getQuery()
                ->getResult();
            if (!empty($xx) && is_array($xx))
                foreach ($xx as $family)
                    if (!$families->contains($family))
                        $families->add($family);
        }
        */
        return $families;
    }

    /**
     * @param Person $person
     * @return ArrayCollection
     */
    public function getPhones(Person $person)
    {
        $families = $this->getFamilies($person);
        $phones = new ArrayCollection();

        foreach($families as $family)
        {
            foreach($family->getPhone() as $phone)
                if (!$phones->contains($phone)) $phones->add($phone);
        }

        return $phones;
    }

    /**
     * @param Person $person
     */
    public function deleteStudent($person)
    {
        if ($this->canDeleteStudent($person, array())) {
            $student = $this->em->getRepository(Student::class)->findOneByPerson($person->getId());
            if ($student instanceof Student)
                $this->em->remove($student);
            $person->setStudentQuestion(false);
            $person->setStudent(null);
            $this->em->persist($person);
            $this->em->flush();
        }
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function createStaff(Person $person)
    {
        if ($this->canBeStaff($person)) {
            $staff = new Staff();
            $staff->setPerson($person);
            $person->setStaffQuestion(true);
            $person->setStaff($staff);
            $this->em->persist($person);
            $this->em->flush();
            return true;
        }
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
     * @return bool
     */
    public function createStudent(Person $person)
    {
        if ($this->canBeStudent($person)) {
            $student = new Student();
            $student->setPerson($person);
            $person->setStudentQuestion(true);
            dump($student);
            $person->setStudent($student);
            $this->em->persist($person);
            $this->em->flush();
            return true;
        }
        return false;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeStudent(Person $person)
    {
        //place rules here to stop new student.
        if ($this->isStaff($person) || $this->isCareGiver($person))
            return false;
        return true;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isStaff(Person $person)
    {
        return $person->getStaffQuestion();
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isCareGiver(Person $person)
    {
        //place rules here to stop new student.
        return false;
    }
}