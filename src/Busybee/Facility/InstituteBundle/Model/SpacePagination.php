<?php

namespace Busybee\Facility\InstituteBundle\Model;

use Busybee\Core\PaginationBundle\Model\PaginationManager;

class SpacePagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Space';

	/**
	 * build Query
	 *
	 * @version    13th December 2016
	 * @since      13th December 2016
	 *
	 * @param    boolean $count
	 *
	 * @return    \Busybee\Core\PaginationBundle\Model\query
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