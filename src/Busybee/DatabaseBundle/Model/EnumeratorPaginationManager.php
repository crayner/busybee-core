<?php

namespace Busybee\DatabaseBundle\Model ;
use Busybee\PaginationBundle\Model\PaginationManager ;

class EnumeratorPaginationManager extends PaginationManager
{
	public function buildQuery()
	{
		$this->query = $this->initiateQuery();
		if ($this->pagination['sortBy'] == 'a.name')
		{
			$this->query->addOrderBy('a.name' , $this->pagination->getSortOrder());
			$this->query->addOrderBy('a.prompt' , $this->pagination->getSortOrder());
		}
		else 
			$this->query->addOrderBy('a.prompt' , $this->pagination->getSortOrder());
		$this->query
			->orwhere('a.name LIKE :search')
			->orwhere('a.prompt LIKE :search')
			->setParameter('search', '%'.$this->getSearch().'%');
		return $this->query;
	}

}