<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\PaginationBundle\Model\PaginationManager;

class TimeTablePagination extends PaginationManager
{
    protected $paginationName = 'TimeTable';
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

        return $this->getQuery();
    }
}