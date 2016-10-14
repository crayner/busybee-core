<?php

namespace Busybee\DatabaseBundle\Model ;

use Busybee\PaginationBundle\Model\PaginationManager ;

class FieldPaginationManager extends PaginationManager
{
	public function buildQuery()
	{
		$this->query = $this->initiateQuery();
		if ($this->getSortBy() == 'a.name')
		{
			$this->query->addOrderBy('a.name' , $this->getSortOrder());
			$this->query->addOrderBy('t.name' , $this->getSortOrder());
		}
		elseif ($this->getSortBy() == 'a.sortkey') {
			$this->query->addOrderBy('a.sortkey' , $this->getSortOrder());
			$this->query->addOrderBy('t.name' , $this->getSortOrder());
			$this->query->addOrderBy('a.name' , $this->getSortOrder());
		}
		else
		{
			$this->query->addOrderBy('t.name' , $this->getSortOrder());
			$this->query->addOrderBy('a.name', $this->getSortOrder());
		}

		$this->query
			->join('a.table', 't')
			->orwhere('a.name LIKE :search')
			->orwhere('t.name LIKE :search')
			->setParameter('search', '%'.$this->getSearch().'%');
		return $this->query;
	}

}