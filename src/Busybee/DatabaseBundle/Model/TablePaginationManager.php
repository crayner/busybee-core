<?php

namespace Busybee\DatabaseBundle\Model ;
use Busybee\PaginationBundle\Model\PaginationManager ;

class TablePaginationManager extends PaginationManager
{
	public function buildQuery()
	{
		$this->query = $this->initiateQuery();
		$this->query->addOrderBy('a.name', $this->getSortOrder());
		$this->query
			->where('a.name LIKE :search')
			->setParameter('search', '%'.$this->getSearch().'%');
		return $this->query;
	}

}