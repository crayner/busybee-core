<?php

namespace Busybee\PaginationBundle\Model ;

use Symfony\Component\HttpFoundation\Request ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container ;
use Doctrine\ORM\EntityRepository ;
use stdClass ;

/**
 * Pagination Manager
 *
 * @version	25th October 2016
 * @since	25th October 2016
 * @author	Craig Rayner
 */
abstract class PaginationManager
{
	/**
	 * @var array
	 */
	private $initialSettings ;

	/**
	 * @var Doctrine\ORM\EntityRepository
	 */
	protected $repository ;

	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $manager ;

	/**
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $container ;

	/**
	 * @var 
	 */
	private $form ;

	/**
	 * @var Query
	 */
	protected $query ;

	/**
	 * @var Result
	 */
	protected $result;

	/**
	 * @var stdClass
	 */
	private $setting;

	/**
	 * @var integer
	 */
	private $limit;

	/**
	 * @var integer
	 */
	private $lastLimit;

	/**
	 * @var array
	 */
	private $searchList;

	/**
	 * @var string
	 */
	private $search;

	/**
	 * @var integer
	 */
	private $total;

	/**
	 * @var integer
	 */
	private $offSet;

	/**
	 * @var integer
	 */
	private $pages;

	/**
	 * @var string
	 */
	private $lastSearch;

	/**
	 * @var array
	 */
	private $control = array();

	/**
	 * @var array
	 */
	private $join = array();

	/**
	 * @var array
	 */
	private $select = array();

	/**
	 * @var string
	 */
	private $alias = 'a';

	/**
	 * Constructor
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	array	$pagination  Pagination Settings from Parameters
	 * @param	Doctrine\ORM\EntityRepository	$repository
	 * @param	Doctrine\ORM\EntityManager	$manager
	 * @return	void
	 */
	public function __construct($pagination, EntityRepository $repository, Container $container)
	{
		$this->setPagination($pagination);
		$this->manager = $container->get('doctrine.orm.entity_manager');
		$this->repository = $repository ;
		$this->form = $container->get('form.factory')->createNamedBuilder('paginator', 'Busybee\PaginationBundle\Form\PaginationType', $this)->getForm();
	}

	/**
	 * Set Pagination
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	array	$pagination  Pagination Settings from Parameters
	 * @return	void
	 */
	private function setPagination($pagination)
	{
		$this->setting = new stdClass();
		$this->initialSettings = $pagination;
		if (! is_array($pagination)) return ;
		foreach ($pagination as $name => $value)
		{
			$setName = 'set'.ucwords($name);
			$this->setting->$name = $value ;
			$this->$setName($value);
		}
		$this->total = 0;
	}
	
	/**
	 * initiate Query
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	boolean		$count
	 * @return	Query
	 */
	protected function initiateQuery($count = false)
	{
		$this->query = $this->repository->createQueryBuilder($this->getAlias());
		if ($count)
			$this->query->select('COUNT('.$this->getAlias().')');
		return $this->query ;
	}
	
	/**
	 * get Data Set
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	array	of Data
	 */
	public function getDataSet()
	{
		$this->pages = intval(ceil($this->getTotal() / $this->getLimit())) ;
		$this->result = $this->buildQuery()
			->setFirstResult($this->getOffSet())
			->setMaxResults($this->getLimit())
			->getQuery()
			->getResult();
		return $this->result;
	}
	
	/**
	 * set Sort By
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	array/string	$sortBy
	 * @return	this
	 */
	public function setSortBy($sortBy)
	{
		if (is_array($sortBy))
		{
			reset($sortBy);
			$sortBy = key($sortBy);
		}
		if (array_key_exists($sortBy, $this->setting->sortBy))
			$this->sortBy = $sortBy ;
		else {
			reset($this->setting->sortBy);
			$this->sortBy = key($this->setting->sortBy);
		}
		return $this ;
	}
	
	/**
	 * get Sort By
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	array
	 */
	public function getSortBy()
	{
		return $this->setting->sortBy[$this->sortBy] ;
	}
	
	/**
	 * get Sort By Name
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	string
	 */
	public function getSortByName()
	{
		return $this->sortBy ;
	}
	
	/**
	 * set Order By
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	this
	 */
	public function setOrderBy()
	{
		if (! empty($this->getSortBy()))
		{
			foreach($this->getSortBy() as $name=>$order)
			{
				$this->query->addOrderBy($name , $order);
			}
		}
		return $this ;
	}
	
	/**
	 * set Limit
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	integer		$limit
	 * @return	this
	 */
	public function setLimit($limit)
	{
		$limit = intval($limit);
		$this->limit = $limit < 10 ? 10 : $limit ;
		return $this ;
	}
	
	/**
	 * get Limit
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	integer		$limit
	 * @return	this
	 */
	public function getLimit()
	{
		$this->limit = intval($this->limit) < 10 ? 10 : intval($this->limit);
		return $this->limit ;
	}
	
	/**
	 * set Search List
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	array	$searchList
	 * @return	this
	 */
	public function setSearchList($searchList)
	{
		$this->searchList = is_array($searchList) ? $searchList : array();
		return $this ;
	}

	/** 
	 * get Search List
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	this
	 */
	public function getSearchList()
	{
		return $this->searchList = is_array($this->searchList) ? $this->searchList : array() ;
	}
	
	/**
	 * set Search
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	array	$searchList
	 * @return	this
	 */
	public function setSearch($search)
	{
		$this->search = filter_var($search);
		return $this ;
	}

	/**
	 * get Search
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	string
	 */
	public function getSearch()
	{
		return $x = empty($this->search) ? '' : '%'.$this->search.'%' ;
	}

	/**
	 * set Search Where
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	string
	 */
	public function setSearchWhere()
	{
		if (! empty($this->getSearch())) {
			$x = 0;
			foreach($this->getSearchList() as $field)
			{
				$this->query->orwhere($field . ' LIKE :search' . $x);
				$this->query->setParameter('search'. $x++, '%'.$this->getSearch().'%');
			}
		}
		return $this ;
	}

	/**
	 * get Total
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	boolean		$count
	 * @return	query
	 */
	public function getTotal()
	{
		$this->total = intval($this->buildQuery(true)
				->getQuery()
				->getSingleScalarResult());
		return $this->total;
	}

	/**
	 * get OffSet
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	integer
	 */
	public function getOffSet()
	{
		if (empty($this->offSet)) $this->offSet = 0;
		return $this->offSet;
	}

	/**
	 * set OffSet
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 ^ @param	integer		$offSet
	 * @return	integer
	 */
	public function setOffSet($offSet)
	{
		$this->offSet = empty($offSet) ? 0 : intval($offSet);
		return $this ;
	}

	/**
	 * get Pages
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	query
	 */
	public function getPages()
	{
		$this->pages = intval($this->pages) < 1 ? 1 : intval($this->pages) ;
		return $this->pages;
	}

	/**
	 * get Form
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	Object
	 */
	public function getForm()
	{
		return $this->form;
	}

	/**
	 * get Sort List
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	array
	 */
	public function getSortList()
	{
		$sortByList = array();
		if (! empty($this->setting->sortBy) && is_array($this->setting->sortBy)) 
			foreach($this->setting->sortBy as $name=>$w)
				$sortByList[$name] = $name;
		return $sortByList;
	}

	/**
	 * get Last Search
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	string
	 */
	public function getLastSearch()
	{
		return $this->lastSearch = empty($this->lastSearch) ? '' : $this->lastSearch ;
	}

	/**
	 * set Last Search
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	this
	 */
	public function setLastSearch($lastSearch)
	{
		$this->lastSearch = $lastSearch ;
		if (empty($lastSearch))
			$this->lastSearch = null;
		return $this ;
	}

	/**
	 * inject Request
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	this
	 */
	public function injectRequest(Request $request)
	{
		$this->getForm()->handleRequest($request);
		if (! $this->form->isSubmitted())
		{
			$this->post = false;
			$this->resetPagination();
			$this->getTotal();
		} else {
			$this->post = true;
			$this->setSearch($this->form['currentSearch']->getData());
			$this->setLastSearch($this->form['lastSearch']->getData());
			$this->setTotal($this->form['total']->getData());
			$this->setOffSet($this->form['offSet']->getData());
			if ($this->getSearch() !== $this->getLastSearch() || $this->form['limit']->getData() !== $this->form['lastLimit']->getData())
				$this->resetPagination();
			$this->setSearch($this->form['currentSearch']->getData());
			$this->setLastSearch($this->form['lastSearch']->getData());
			$this->setLimit($this->form['limit']->getData());
			$this->setLastLimit($this->form['lastLimit']->getData());
			$this->setSortBy($this->form['currentSort']->getData());
			$this->getTotal();
			$this->managePost();
		}
		$this->form = $this->getForm()->createView();
		return $this;
	}

	/**
	 * Reset Pagination
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	this
	 */
	private function resetPagination()
	{
		$this->setPagination($this->initialSettings);
		$this->total = 0;
		$this->offSet = 0;
		return $this ;
	}

	/**
	 * Manage Post
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	this
	 */
	public function managePost()
	{
		if ($this->form->get('prev')->isClicked())
			$this->getPrev();
		if ($this->form->get('next')->isClicked())
			$this->getNext();	
		// if ajax is used then ....
		switch ($this->control)
		{
			case 'paginatorNext':
				$this->getNext();
				break;
			case 'paginatorPrev':
				$this->getPrev();
				break;
			case 'paginatorLimit':
				$this->checkLimit();
				break;
			case 'paginatorSort':
				break;
		}
		return $this ;
	}

	/**
	 * set Total
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	integer		$total
	 * @return	this
	 */
	public function setTotal($total)
	{
		$this->total = intval($total) > 0 ? 0 : intval($total) ;
		return $this;
	}

	/**
	 * get Next
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	this
	 */
	private function getNext()
	{
		$offSet = $this->getOffSet();
		if ($this->getOffSet() + $this->getLimit() < $this->getTotal())
			$offSet = $this->getOffSet() + $this->getLimit();
		$this->checkOffSet($offSet) ;
	}

	/**
	 * get Prev
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	this
	 */
	private function getPrev()
	{
		$offSet = $this->getOffSet() - $this->getLimit();
		$this->checkOffset($offSet) ;
	}

	/**
	 * check OffSet
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	this
	 */
	private function checkOffSet($offSet)
	{
		if ($offSet >= $this->total)
			$offSet = $this->total - $this->getLimit();
		if ($offSet < 0)
			$offSet = 0;
		$this->setOffset($offSet);
		return $offSet ;
	}

	/**
	 * get Current Page
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	integer
	 */
	public function getCurrentPage()
	{
		return intval($this->getOffSet() / $this->getLimit()) + 1;
	}

	/**
	 * get Total Pages
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	integer
	 */
	public function getTotalPages()
	{
		return $this->pages;
	}
	
	/**
	 * get Result
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @return	array	of Data
	 */
	public function getResult()
	{
		return $this->result;
	}
	
	/**
	 * set Last Limit
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	integer		$limit
	 * @return	this
	 */
	public function setLastLimit($limit)
	{
		$limit = intval($limit);
		$this->lastLimit = $limit < 10 ? 10 : $limit ;
		return $this ;
	}
	
	/**
	 * get Last Limit
	 *
	 * @version	25th October 2016
	 * @since	25th October 2016
	 * @param	integer		$limit
	 * @return	this
	 */
	public function getLastLimit()
	{
		$this->lastLimit = intval($this->lastLimit) < 10 ? 10 : intval($this->lastLimit);
		return $this->lastLimit ;
	}
	
	/**
	 * set Join
	 *
	 * @version	27th October 2016
	 * @since	27th October 2016
	 * @return	this
	 */
	private function setJoin($join)
	{
		$this->join = $join;
		return $this ;
	}
	
	/**
	 * set Select
	 *
	 * @version	27th October 2016
	 * @since	27th October 2016
	 * @return	this
	 */
	private function setSelect($select)
	{
		$this->select = $select;
		return $this ;
	}
	
	/**
	 * set Join
	 *
	 * @version	27th October 2016
	 * @since	27th October 2016
	 * @return	this
	 */
	protected function setQueryJoin()
	{
		if (! is_array($this->join)) return $this ;
		foreach ($this->join as $name=>$pars)
		{
			$type = empty($pars['type']) ? 'join' : $pars['type'] ;
			$this->query->$type($name, $pars['alias']);
		}
		return $this ;
	}
	
	/**
	 * set Select
	 *
	 * @version	27th October 2016
	 * @since	27th October 2016
	 * @return	this
	 */
	protected function setQuerySelect()
	{
		if (! is_array($this->select)) return $this ;
		foreach ($this->select as $name)
			$this->query->addSelect($name);
		return $this ;
	}
	
	/**
	 * set Alias
	 *
	 * @version	27th October 2016
	 * @since	27th October 2016
	 * @return	this
	 */
	private function setAlias($alias)
	{
		$this->alias = $alias;
		return $this ;
	}
	
	/**
	 * set Select
	 *
	 * @version	27th October 2016
	 * @since	27th October 2016
	 * @return	this
	 */
	public function getAlias()
	{
		return $this->alias ;
	}
}