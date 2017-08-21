<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\PaginationBundle\Model\PaginationManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityRepository;

class LinePagination extends PaginationManager
{
    protected $paginationName = 'line';

    /**
     * @var Year
     */
    private $year;

    /**
     * Constructor
     *
     * @version    25th October 2016
     * @since    25th October 2016
     * @param    array $pagination Pagination Settings from Parameters
     * @param    EntityRepository $repository
     * @param   Container $container
     */
    public function __construct($pagination, EntityRepository $repository, Container $container, Year $year)
    {
        $this->year = $year;
        parent::__construct($pagination, $repository, $container);
    }

    /**
     * build Query
     *
     * @version    22nd March 2017
     * @since    22nd March 2017
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
            ->leftJoin('l.year', 'y')
            ->andWhere('y.id = :year_id')
            ->setParameter('year_id', $this->year->getId());
        return $this->getQuery();
    }
}