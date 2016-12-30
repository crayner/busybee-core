<?php
namespace Busybee\FamilyBundle\Model;

use Busybee\PaginationBundle\Model\PaginationManager ;

class FamilyPagination extends PaginationManager
{

	/**
	 * build Query
	 *
	 * @version	28th October 2016
	 * @since	28th October 2016
	 * @param	boolean		$count
	 * @return	query
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