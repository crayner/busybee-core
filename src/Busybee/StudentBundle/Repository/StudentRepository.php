<?php

namespace Busybee\StudentBundle\Repository;

use Busybee\StudentBundle\Entity\Student;
use Doctrine\ORM\EntityRepository;

/**
 * StudentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StudentRepository extends EntityRepository
{
    /**
     * @param   integer $personID
     * @return  Student
     */
    public function findOneByPerson($personID)
    {
        $student = parent::findOneByPerson($personID);
        return $student instanceof Student ? $student : new Student();
    }
}
