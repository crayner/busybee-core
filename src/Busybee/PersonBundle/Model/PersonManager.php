<?php
namespace Busybee\PersonBundle\Model;


use Busybee\PersonBundle\Entity\CareGiver;
use Busybee\PersonBundle\Entity\Person;
use Busybee\PersonBundle\Entity\Staff;
use Busybee\PersonBundle\Entity\Student;
use Busybee\SecurityBundle\Entity\User;
use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\ORM\EntityManager;

class PersonManager
{
    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * @var EntityManager
     */
    private $em ;

    /**
     * PersonManager constructor.
     * @param SettingManager $sm
     */
    public function __construct(SettingManager $sm, EntityManager $em)
    {
        $this->sm = $sm ;
        $this->em = $em ;
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
            return false ;
        return true ;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isCareGiver(Person $person)
    {
        $carer = $this->em->getRepository(CareGiver::class)->findOneByPerson($person->getId());
        if ($carer instanceof CareGiver)
            return true ;
        return false ;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isStaff(Person $person)
    {
        $staff = $this->em->getRepository(Staff::class)->findOneByPerson($person->getId());
        if ($staff instanceof Staff)
            return true ;
        return false ;
    }

    /**
     * @param Person $person
     * @param $parameters
     */
    public function deleteStudent(Person $person, $parameters)
    {
        if ($this->canDeleteStudent($person, $parameters)) {
            $student = $this->em->getRepository(Student::class)->findOneByPerson($person->getId());
            if (is_array($parameters))
                foreach ($parameters as $data)
                    if (isset($data['data']['name']) && isset($data['entity']['name'])) {
                        $client = $this->em->getRepository($data['entity']['name'])->findOneByStudent($student->getId());
                        if (is_object($client))
                            $this->em->remove($client);
                    }
            $this->em->remove($student);
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
        if (is_array($parameters))
            foreach($parameters as $data)
                if (isset($data['data']['name']) && isset($data['entity']['name']))
                {
                    $client = $this->em->getRepository($data['entity']['name'])->findOneByStudent($student->getId());

                    if (is_object($client) && $client->getId() > 0)
                        return false ;
                }
        return $student->canDelete() ;
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
            return false ;

        return $careGiver->canDelete() ;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeCareGiver(Person $person)
    {
        //plcae rules here to stop new student.
        if ($this->isStudent($person))
            return false ;
        return true ;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isStudent(Person $person)
    {
        $student = $this->em->getRepository(Student::class)->findOneByPerson($person->getId());
        if ($student instanceof Student)
            return true ;
        return false ;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeStaff(Person $person)
    {
        //plcae rules here to stop new student.
        if ($this->isStudent($person))
            return false ;
        return true ;
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
            return false ;

        return $staff->canDelete() ;
    }

    /**
     * @return SettingManager
     */
    public function getSettingManager()
    {
        return $this->sm ;
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeUser(Person $person)
    {
        if (empty($person->getEmail()))
            return false ;
        return true ;
    }

    /**
     * @param Person $person
     */
    public function deleteUser(Person $person)
    {
        if ($this->canDeleteUser($person)) {
            $staff = $this->em->getRepository(User::class)->findOneByPerson($person->getId());
            $this->em->remove($staff);
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
        if (! $user instanceof User)
            return false ;

        return $user->canDelete() ;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->sm->getParameter($name);
    }
}