<?php
namespace Busybee\InstituteBundle\Model;

use Busybee\PaginationBundle\Model\PaginationManager ;

class SpacePagination extends PaginationManager
{
    protected $paginationName = 'Space';

	/**
	 * build Query
	 *
	 * @version	13th December 2016
	 * @since	13th December 2016
	 * @param	boolean		$count
	 * @return	\Busybee\PaginationBundle\Model\query
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
		
		return $this->query ;
	}
}