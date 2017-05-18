<?php

namespace Busybee\StudentBundle\Model;

use Busybee\PaginationBundle\Model\PaginationManager;

class StudentPagination extends PaginationManager
{

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
            ->setParameter('stu_q', true);

        return $this->query;
    }
}