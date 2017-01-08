<?php

namespace Busybee\PersonBundle\Model ;

use Busybee\FamilyBundle\Repository\FamilyRepository;

/**
 * Care Giver Model
 *
 * @version	31st October 2016
 * @since	31st October 2016
 * @author	Craig Rayner
 */
abstract class CareGiverModel
{
	use \Busybee\PersonBundle\Model\FormatNameExtension ;

    /**
     * @return bool
     */
    public function canDelete()
    {
        //Place rules here to stop delete

        return true ;
    }

    /**
     * @param FamilyRepository $fr
     * @return array
     */
    public function getFamilies(FamilyRepository $fr)
    {
        return $fr->createQueryBuilder('f')
            ->leftJoin('f.emergencyContact', 's')
            ->where('s.id = :studentId')
            ->setParameter('studentId', $this->getId())
            ->getQuery()
            ->getResult();
    }
}