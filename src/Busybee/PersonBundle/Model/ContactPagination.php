<?php

namespace Busybee\PersonBundle\Model;

use Busybee\PaginationBundle\Model\PaginationManager;

class ContactPagination extends PaginationManager
{
    protected $paginationName = 'Person';

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

        $this->query->andWhere('p.studentQuestion = :stu_q')
            ->andWhere('p.staffQuestion = :staff_q')
            ->setParameter('stu_q', false)
            ->setParameter('staff_q', false);

        return $this->query;
    }
}