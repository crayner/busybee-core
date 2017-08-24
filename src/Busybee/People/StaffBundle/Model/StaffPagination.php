<?php

namespace Busybee\People\StaffBundle\Model;

use Busybee\Core\PaginationBundle\Model\PaginationManager;

class StaffPagination extends PaginationManager
{
	protected $paginationName = 'Person';

	/**
	 * build Query
	 *
	 * @version    28th October 2016
	 * @since      28th October 2016
	 *
	 * @param    boolean $count
	 *
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

		$this->getQuery()->andWhere('p.staffQuestion = :staff_q')
			->setParameter('staff_q', true);

		return $this->getQuery();
	}
}