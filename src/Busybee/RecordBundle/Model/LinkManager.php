<?php

namespace Busybee\RecordBundle\Model ;

class LinkManager 
{
	private $parent ;
	private $child ;
	private $identifiers ;
	private $recordEntity ;
	private $parentTable ;
	private $childTable ;
	
	public function __construct( RecordEntityManager $recordEntity )
	{
		$this->recordEntity = $recordEntity;
		$this->parent = new \STDClass ;
		$this->child = new \STDClass ;
	}
	
	public function getParentValue($name)
	{
		return $this->parent->$name;
	}
	
	public function setParentValue($name, $value)
	{
		$name = strtolower($name);

		if (! in_array($name, array('table', 'data', 'display', 'relationship')))
			throw new \InvalidArgumentException('Parent links is trying to set an invalid data type.');

		$this->parent->$name = $value;

		return $this;
	}
	
	public function getChildValue($name)
	{
		return $this->child->$name;
	}
	
	public function setChildValue($name, $value)
	{
		$name = strtolower($name);

		if (! in_array($name, array('table', 'data', 'display')))
			throw new \InvalidArgumentException('Child links is trying to set an invalid data type.');

		$this->child->$name = $value;

		return $this;
	}
	public function getParent()
	{
		return $this->parent;
	}
	
	public function setParent($parent)
	{
		$this->parent = $parent ;

		return $this;
	}
	
	public function getChild()
	{
		return $this->child;
	}
	
	public function setChild($value)
	{
		$this->child = $value;

		return $this;
	}

	/**
	 * Table
	 * @return	\busybee\DatabaseBundle\Entity\Table
	 */
	public function getParentTable()
	{
		$name = $this->getParentValue('table');
		if (empty($name))
			throw new \InvalidArgumentException('The Parent Table is not set correctly.');
		$this->parentTable = $this->recordEntity->getTableRepository()->findOneBy(array('name' => $name));
		if (empty($this->parentTable))
			throw new \InvalidArgumentException('The Parent table '.$name.' was not found!');
		return $this->parentTable ;
	}

	/**
	 * get Parent Data ID
	 *
	 * @return	integer
	 */
	public function getParentData()
	{
		$name = $this->getParentValue('data');
		if (empty($name))
			throw new \InvalidArgumentException('The Parent Data is not set correctly.');
		if ($name == 'record_id')
			return NULL;
		$query = $this->recordEntity->getFieldRepository()->createQueryBuilder('f');
		$field = $query->select('f.id')
			->LeftJoin('f.table', 't')
			->where('f.name = :fieldName')->setParameter('fieldName', str_replace(' ', '_', $name))
			->andWhere('t.name = :tableName')->setParameter('tableName', $this->parentTable->getName())
			->getQuery()
			->getResult();
			
		if (empty($field))
			throw new \InvalidArgumentException('The Parent data field '.$name.' was not found in table '.$this->parentTable->getName().'.');
		return $field[0]['id'];
	}

	/**
	 * get Parent Display ID
	 *
	 * @return	integer
	 */
	public function getParentDisplay()
	{
dump($this);
		$name = $this->getParentValue('display');
		if (empty($name))
			throw new \InvalidArgumentException('The Parent Display is not set correctly.');
		if (is_array($name))
		{
			$result = array();
			foreach($name as $q=>$fname)
			{
			$query = $this->recordEntity->getFieldRepository()->createQueryBuilder('f');
			$field = $query->select('f.id')
				->LeftJoin('f.table', 't')
				->where('f.name = :fieldName')->setParameter('fieldName', str_replace(' ', '_', $fname))
				->andWhere('t.name = :tableName')->setParameter('tableName', $this->parentTable->getName())
				->getQuery()
				->getResult();
				if (empty($field))
					throw new \InvalidArgumentException('The Parent display field '.$fname.' was not found in table '.$this->parentTable->getName().'.');
				$result[$q] =$field[0]['id']; 
			}
			return $result;
		}
		else
		{
			$query = $this->recordEntity->getFieldRepository()->createQueryBuilder('f');
			$field = $query->select('f.id')
				->LeftJoin('f.table', 't')
				->where('f.name = :fieldName')->setParameter('fieldName', str_replace(' ', '_', $name))
				->andWhere('t.name = :tableName')->setParameter('tableName', $this->parentTable->getName())
				->getQuery()
				->getResult();
			if (empty($field))
				throw new \InvalidArgumentException('The Parent display field '.$name.' was not found in table '.$this->parentTable->getName().'.');
		}
		return $field[0]['id'];
	}
}
