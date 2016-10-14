<?php

namespace Busybee\DatabaseBundle\Model ;
use Busybee\PaginationBundle\Model\PaginationManager ;

class ValidatorPaginationManager extends PaginationManager
{
	public function buildQuery()
	{
		$this->query = $this->initiateQuery();
		$this->query->addOrderBy('a.name', $this->pagination->getSortOrder());
		$this->query
			->where('a.name LIKE :search')
			->setParameter('search', '%'.$this->getSearch().'%');
		return $this->query;
	}
}