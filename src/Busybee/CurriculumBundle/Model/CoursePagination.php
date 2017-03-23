<?php
namespace Busybee\CurriculumBundle\Model;

use Busybee\PaginationBundle\Model\PaginationManager;

class CoursePagination extends PaginationManager
{

    /**
     * build Query
     *
     * @version    22nd March 2017
     * @since    22nd March 2017
     * @param    boolean $count
     * @return    ORM Query
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

        return $this->query;
    }
}