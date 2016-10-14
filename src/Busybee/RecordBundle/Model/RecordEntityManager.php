<?php

namespace Busybee\RecordBundle\Model ;

class RecordEntityManager
{
	private $tableRepo ;
	private $fieldRepo ;
	private $container ;
	
	public function __construct($tableRepo, $fieldRepo, $container)
	{
		$this->tableRepo = $tableRepo;
		$this->fieldRepo = $fieldRepo;
		$this->container = $container;
	}
	
	public function getTableRepository()
	{
		return $this->tableRepo;
	}
	
	public function getFieldRepository()
	{
		return $this->fieldRepo;
	}

	public function getContainer()
	{
		return $this->container;
	}
	/**
	 * @return 	integer		Count the number of fields in the database
	 */
	public function fieldCount($field)
	{
		$type = $this->getType($field);
		$query = $this->container->get('record.'.$type.'.repository')->createQueryBuilder('r');
		$count = $query->select('count(r.id)')
			->where('r.field = :fieldId')->setParameter('fieldId', $field->getId())
			->getQuery()
			->getSingleScalarResult();
		return intval($count);
	}
	/**
	 * @return 	string		Type
	 */
	public function getType($field)
	{
		$type = $field->getType();
		if (strpos($type, 'enum_') !== false)
			return 'enum';
		return $type;
	}
}