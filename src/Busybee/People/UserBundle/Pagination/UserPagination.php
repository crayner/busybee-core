<?php

namespace Busybee\People\UserBundle\Pagination;

use Busybee\Core\PaginationBundle\Model\PaginationManager;

class UserPagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Person';

	/**
	 * build Query
	 *
	 * @version    28th October 2016
	 * @since      28th October 2016
	 *
	 * @param    boolean $count
	 *
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

		$this->getQuery()
			->andWhere('u.id > :zero')
			->setParameter('zero', 0);

		return $this->getQuery();
	}
}