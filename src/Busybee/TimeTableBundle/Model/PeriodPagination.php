<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\PaginationBundle\Model\PaginationManager;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Doctrine\ORM\EntityRepository;

class PeriodPagination extends PaginationManager
{
    /**
     * @var string
     */
    protected $paginationName = 'period';

    /**
     * @var TimeTable
     */
    private $tt;

    /**
     * Constructor
     *
     * @version    25th October 2016
     * @since    25th October 2016
     * @param    array $pagination Pagination Settings from Parameters
     * @param    EntityRepository $repository
     * @param   Container $container
     */
    public function __construct($pagination, EntityRepository $repository, Container $container, TimeTable $tt)
    {
        $this->setTimeTable($tt);
        parent::__construct($pagination, $repository, $container);
    }

    public function setTimeTable(TimeTable $tt)
    {
        $this->tt = $tt;

        return $this;
    }

    /**
     * build Query
     *
     * @version 22nd March 2017
     * @since   22nd March 2017
     * @param   boolean $count
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

        if ($this->tt instanceof TimeTable)
            $this->getQuery()
                ->leftJoin('c.timetable', 't')
                ->andWhere('t.id = :tt_id')
                ->setParameter('tt_id', $this->tt->getId());
        else
            throw new \Exception('The timetable has not been injected into the Period Paginator.');

        return $this->getQuery();
    }

    public function getTimeTable()
    {
        return $this->tt;
    }
}