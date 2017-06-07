<?php

namespace Busybee\StudentBundle\Model;

use Busybee\InstituteBundle\Entity\Year;
use Busybee\PaginationBundle\Model\PaginationManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class ActivityPagination extends PaginationManager
{
    protected $paginationName = 'Activity';

    /**
     * @var Year
     */
    protected $systemYear;

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
    public function __construct($pagination, EntityRepository $repository, Container $container, Year $systemYear)
    {
        $this->systemYear = $systemYear;
        parent::__construct($pagination, $repository, $container);
    }

    /**
     * build Query
     *
     * @version    28th October 2016
     * @since    28th October 2016
     * @param    boolean $count
     * @return    query
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

        $this->query
            ->andWhere('y.id = :systemYear')
            ->setParameter('systemYear', $this->getSystemYear()->getId());

        return $this->query;
    }

    public function getSystemYear()
    {
        return $this->systemYear;
    }
}