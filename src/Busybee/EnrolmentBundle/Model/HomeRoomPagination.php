<?php
namespace Busybee\EnrolmentBundle\Model;

use Busybee\PaginationBundle\Model\PaginationManager ;

class HomeRoomPagination extends PaginationManager
{

	/**
	 * build Query
	 *
	 * @version	13th December 2016
	 * @since	13th December 2016
	 * @param	boolean		$count
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

		return $this->query ;
	}
}