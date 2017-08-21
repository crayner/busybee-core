<?php

namespace Busybee\InstituteBundle\Model;


use Busybee\InstituteBundle\Entity\Grade;
use Doctrine\Common\Persistence\ObjectManager;
use Busybee\Core\CalendarBundle\Entity\Year;

class GradeManager
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var Year
     */
    private $year;

    /**
     * GradeManager constructor.
     * @param ObjectManager $om
     * @param Year $year
     */
    public function __construct(ObjectManager $om, Year $year)
    {
        $this->om = $om;
        $this->year = $year;
    }

    /**
     * @return mixed
     */
    public function getYearGrades()
    {
        return $this->om->getRepository(Grade::class)->createQueryBuilder('g')
            ->leftJoin('g.year', 'y')
            ->where('y.id = :year_id')
            ->setParameter('year_id', $this->year->getId())
            ->orderBy('g.sequence', 'ASC')
            ->getQuery()
            ->getResult();
    }
}