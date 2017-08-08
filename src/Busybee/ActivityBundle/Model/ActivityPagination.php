<?php

namespace Busybee\ActivityBundle\Model;

use Busybee\InstituteBundle\Entity\Year;
use Busybee\PaginationBundle\Model\PaginationManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Busybee\TimeTableBundle\Model\ActivityManager;

class ActivityPagination extends PaginationManager
{
    protected $paginationName = 'Activity';

    /**
     * @var Year
     */
    protected $systemYear;

    protected $activityManager;

    /**
     * Constructor
     *
     * @version    7th June 2017
     * @since    7th June 2017
     * @param    array $pagination Pagination Settings from Parameters
     * @param    EntityRepository $repository
     * @param    Container $container
     * @param    Year $systemYear
     * @param   Container $container
     */
    public function __construct($pagination, EntityRepository $repository, Container $container, Year $systemYear, ActivityManager $manager)
    {
        $this->systemYear = $systemYear;
        $this->activityManager = $manager;
        parent::__construct($pagination, $repository, $container);
    }

    /**
     * build Query
     *
     * @version    28th October 2016
     * @since    28th October 2016
     * @param    boolean $count
     * @return \Doctrine\ORM\Query
     */
    public function buildQuery($count = false)
    {
        $this->initiateQuery($count);
        if ($count)
            $this
                ->setQueryJoin()
                ->setSearchWhere();
        else
            $this
                ->setQuerySelect()
                ->setQueryJoin()
                ->setOrderBy()
                ->setSearchWhere();

        $this->getQuery()
            ->andWhere('y.id = :systemYear')
            ->setParameter('systemYear', $this->getSystemYear()->getId());

        return $this->getQuery();
    }

    public function getSystemYear()
    {
        return $this->systemYear;
    }

    public function getManager(): ActivityManager
    {
        return $this->activityManager;
    }
}