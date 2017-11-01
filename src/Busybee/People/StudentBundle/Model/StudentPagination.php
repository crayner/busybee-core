<?php

namespace Busybee\People\StudentBundle\Model;

use Busybee\Core\PaginationBundle\Model\PaginationManager;
use Busybee\People\PersonBundle\Model\PersonManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class StudentPagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Person';

	/**
	 * @var PersonManager
	 */
	private $personManager;

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
		if ($this->personManager->gradesInstalled())
		{
			$this->addJoin('s.grades', 'leftJoin', 'sg');
			$this->addJoin('sg.grade', 'leftJoin', 'g');
			$this->addJoin('g.year', 'leftJoin', 'y');
			$this->addSearchList('g.grade');
			$this->addSearchList('y.name');
		}

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

		if ($this->personManager->gradesInstalled())
		{
			$this->getQuery()
				->andWhere('(sg.status = :currentStatus OR sg.id IS NULL)')
				->setParameter('currentStatus', 'Current');
		}

		return $this->getQuery();
	}

	/**
	 * Constructor
	 *
	 * @version    25th October 2016
	 * @since      25th October 2016
	 *
	 * @param    array            $pagination Pagination Settings from Parameters
	 * @param    EntityRepository $repository
	 * @param   Container         $container
	 */
	public function __construct($pagination, EntityRepository $repository, Container $container, PersonManager $personManager)
	{
		$this->personManager = $personManager;
		parent::__construct($pagination, $repository, $container);
	}
}