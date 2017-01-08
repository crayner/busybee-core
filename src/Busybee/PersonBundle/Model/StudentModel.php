<?php

namespace Busybee\PersonBundle\Model ;

use Busybee\FamilyBundle\Repository\FamilyRepository;

/**
 * Student Model
 *
 * @version	31st October 2016
 * @since	31st October 2016
 * @author	Craig Rayner
 */
abstract class StudentModel
{
	use \Busybee\PersonBundle\Model\FormatNameExtension ;

    /**
     * Student constructor.
     */
    public function __construct()
    {
        $this->setStartAtThisSchool(new \DateTime());
        $this->setStartAtSchool(new \DateTime());
    }

    /**
     * @return bool
     */
    public function canDelete()
    {
        return true ;
    }

    /**
     * @param FamilyRepository $fr
     * @return array
     */
    public function getFamilies(FamilyRepository $fr)
    {
        return $fr->createQueryBuilder('f')
            ->leftJoin('f.students', 's')
            ->where('s.id = :studentId')
            ->setParameter('studentId', $this->getId())
            ->getQuery()
            ->getResult();
    }
}