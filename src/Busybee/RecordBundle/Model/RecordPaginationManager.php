<?php

namespace Busybee\RecordBundle\Model ;

use Busybee\PaginationBundle\Model\PaginationManager ;
use Doctrine\ORM\EntityManager ;
use Symfony\Component\DependencyInjection\ContainerInterface ;

class RecordPaginationManager extends PaginationManager
{
	private $title;
	private $container ;
	private $list ;
	
	public function __construct($pagination, $repository, EntityManager $manager, ContainerInterface $container)
	{
		$this->container = $container ;
		parent::__construct($pagination, $repository, $manager);
	}
	
	public function buildQuery()
	{
		$this->query = $this->initiateQuery();

		return $this->query;
	}
	
	public function recordDefaults($table_form)
	{
		$this->title = $table_form;
		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}
	
	public function listManager()
	{
		//Look for a form First, then default to the table...
		$this->list = array();
		$repo = $this->container->get('form.repository');
		$this->list = $repo->findOneBy(array('name' => $this->title));
		if (empty($display))
		{
			$repo = $this->container->get('table.repository');
			$this->list = $repo->findOneBy(array('name' => $this->title));
		}
dump($repo);
		return $this->list;
	}
}