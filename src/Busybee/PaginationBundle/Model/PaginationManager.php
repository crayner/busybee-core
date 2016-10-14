<?php

namespace Busybee\PaginationBundle\Model ;

use Symfony\Component\HttpFoundation\Request ;
use Doctrine\ORM\EntityManager ;

abstract class PaginationManager implements PaginationManagerInterface
{
	protected 	$default ;
	protected 	$repository ;
	protected 	$manager ;
	protected	$form ;
	private 	$total = false;
	protected 	$query;
	protected 	$post = false;
	private		$sortOrder ;
	private		$search ;
	private		$searchList ;
	private		$searchIn ;
	private		$last_search ;
	private		$limit ;
	private		$offset ;
	private		$sortBy ;
	private		$sortList ;
	private		$control ;
	
	public function __construct($pagination, $repository, EntityManager $manager)
	{
		$this->default = $this->setPagination($pagination);
		$this->manager = $manager;
		$this->repository = $repository ;
		$this->form = NULL ;
	}
	
	public function injectForm( \Symfony\Component\Form\FormInterface $form)
	{
		$this->form = $form;
		$this->checkRequest();
	}

	public function getPagination()
	{
		return $this ;
	}

	private function checkRequest()
	{
		if (! $this->form->isSubmitted())
		{
			$this->post = false;
			$paginator = array();
			$paginator = $this->resetPagination();
			$this->getTotal();
		} else {
			$this->post = true;
			$this->setSearch($this->form['current_search']->getData());
			$this->setLastSearch($this->form['last_search']->getData());
			$this->setTotal($this->form['total']->getData());
			if ($this->getSearch() !== $this->getLastSearch())
				$this->resetPagination();
			$this->setSearch($this->form['current_search']->getData());
			$this->setLastSearch($this->form['last_search']->getData());
			$this->setLimit($this->form['limit']->getData());
			$this->setSortBy($this->form['current_sort']->getData());
			$this->setOffSet($this->form['offset']->getData());
			$this->getTotal();
			$this->managePost();
		}
	}

	public function managePost()
	{
		if ($this->form->get('prev')->isClicked())
			$this->getPrev();
		if ($this->form->get('next')->isClicked())
			$this->getNext();	
		// if ajax is used then ....
		switch ($this->control)
		{
			case 'paginator_next':
				$this->getNext();
				break;
			case 'paginator_prev':
				$this->getPrev();
				break;
			case 'paginator_limit':
				$this->checkLimit();
				break;
			case 'paginator_sort':
				break;
		}
	}
	
	private function getNext()
	{
		$offset = $this->getOffset();
		if ($this->getOffSet() + $this->getLimit() < $this->getTotal())
			$offset = $this->getOffSet() + $this->getLimit();
		$this->checkOffset($offset) ;
	}
	
	private function checkOffset($offset)
	{
		if ($offset >= $this->total)
			$offset = $this->total - $this->getLimit();
		if ($offset < 0)
			$offset = 0;
		$this->setOffset($offset);
		return $offset ;
	}
	
	private function getPrev()
	{
		$offset = $this->getOffSet() - $this->getLimit();
		$this->checkOffset($offset) ;
	}
	
	public function initiateQuery()
	{
		return $this->query = $this->repository->createQueryBuilder('a');
	}
	
	public function executeQuery()
	{
		if ($this->getLimit() < 10)
			$this->SetLimit($this->default['limit']);
		return $result = $this->buildQuery()
				->setFirstResult( $this->getOffset() )
				->setMaxResults( $this->getLimit() )
				->getQuery()
				->getResult();
	}
	
	private function resetPagination()
	{
		$this->setPagination($this->default);
		$this->total = false;
		$this->offset = 0;
	}

	public function getTotal()
	{
		if ($this->total === false)
			$this->total = count($this->buildQuery()
				->getQuery()
				->getResult());
		
		return $this->total;
	}

	public function setTotal($total)
	{
		if (intval($total) === 0)
			$this->total = false;
		else
			$this->total = intval($total);

		return $this;
	}

	private function handleSort($list) 
	{
		$offset = 0;
		foreach ($list as $q=>$w)
		{
			if (strpos($q, 'offset-') === 0){
				$offset = intval(str_replace('offset-', '', $q));
				unset($list[$q]);
				break ;
			}
		}
		foreach ($list as $q=>$w)
		{
			$id = intval(substr($q, 6));
			$entity = $this->repository->find($id);
			$entity->setSortkey($offset++);
			$this->manager->persist($entity);
		}
		$this->manager->flush();
	}
	
	public function getSortOrder()
	{
		return $this->sortOrder;
	}

	public function setSortOrder($sortOrder)
	{
		$this->sortOrder = $sortOrder ;
		return $this ;
	}

	public function getSearch()
	{
		return $this->search;
	}
	
	public function setSearch($search)
	{
		$this->search = $search;
		if (empty($search))
			$this->search = NULL;
		return $this;
	}
	
	public function getLimit()
	{
		return $this->limit;
	}
	
	public function setLimit($limit)
	{
		$this->limit = intval($limit) ;
		return $this ;
	}
	
	public function getOffset()
	{
		return $this->offset;
	}

	public function setOffset($offset)
	{
		$this->offset = intval($offset);
		return $this ;
	}

	public function getSortList()
	{
		return $this->sortList ;
	}

	public function setSortList($sortList)
	{
		$this->sortList = $sortList ;
		return $this ;
	}

	public function getSortBy()
	{
		return $this->sortBy ;
	}
	
	public function getSort()
	{
		return $this->getSortBy() ;
	}

	public function setSortBy($sortBy)
	{
		$this->sortBy = $sortBy ;
		return $this ;
	}

	public function setSearchList($searchList)
	{
		$this->searchList = $searchList ;
		return $this ;
	}

	public function setSearchIn($searchIn)
	{
		$this->searchIn = $searchIn ;
		return $this ;
	}

	public function getLastSearch()
	{
		return $this->last_search ;
	}

	public function setLastSearch($last_search)
	{
		$this->last_search = $last_search ;
		if (empty($last_search))
			$this->last_search = NULL;
		return $this ;
	}

	private function setPagination($pagination)
	{
		foreach ($pagination as $name => $value)
		{
			$setName = 'set'.ucwords($name);
			$this->$setName($value);
		}
		$this->total = false;
		return $pagination;
	}

	public function controlButton($request)
	{
		$this->control = $request->request->get('subBut');
		return $this ;
	}

	private function checkLimit()
	{
		$this->checkOffset($this->getOffset());
	}
	
	public function listManager()
	{
		return $this->executeQuery();
	}
}