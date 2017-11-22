<?php

namespace Busybee\Core\CalendarBundle\Pagination;

use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\Core\PaginationBundle\Model\PaginationManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GradePagination extends PaginationManager
{
	/**
	 * @var string
	 */
	protected $paginationName = 'Grade';
	/**
	 * @var Year
	 */
	private $year;

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
	public function __construct($pagination, EntityRepository $repository, ContainerInterface $container)
	{
		$this->year = $container->get('busybee_core_calendar.model.current_year')->getCurrentYear();

		return parent::__construct($pagination, $repository, $container);
	}

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

		$this->getQuery()->andWhere('y.id = :currentYear')
			->setParameter('currentYear', $this->year->getId());

		return $this->getQuery();
	}
}