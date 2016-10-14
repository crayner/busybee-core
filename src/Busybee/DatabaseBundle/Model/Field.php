<?php

namespace Busybee\DatabaseBundle\Model;

use Symfony\Component\Yaml\Parser ;
use Symfony\Component\Yaml\Dumper ;
/**
 * Field Base
 */
class Field
{
	private $was;
	
	public function __construct()
	{
		$this->setSortKey(0);
		$this->was = NULL;
	}

	/**
	 * @return Busybee\DatabaseBundle\Entity\Table
	 */
	public function getSelectTable()
	{
		$tables = $this->getTable();
		if ( $tables instanceof \Doctrine\Common\Collections\ArrayCollection)
			$table = $tables->first();
		elseif ( $tables instanceof \Doctrine\ORM\PersistentCollection)
			$table = $tables->first();
		elseif ( is_array($tables) )
			$table = array_shift($tables);
		else
			$table = null;
		return $table;
	}

	/**
	 * @return Busybee\SecurityBundle\Entity\Role
	 */
	public function getSelectRole()
	{
		$roles = $this->getRole();
		if ( $roles instanceof \Doctrine\Common\Collections\ArrayCollection)
			$role = $roles->first();
		elseif ( $roles instanceof \Doctrine\ORM\PersistentCollection)
			$role = $roles->first();
		elseif ( is_array($roles) )
			$role = array_shift($roles);
		else
			$role = null;
		if (empty($role))
		{
			$table = $this->getSelectTable();
			if (! empty($table))
				$role = $table->getSelectRole();
		}
		return $role;
	}
	
	/**
	* get Yaml
	* @param	string 
	* @return 	string yaml formated
	*/
	public function parseYaml($value)
	{
		$this->yaml = new Parser();
		return $this->yaml->parse($value);
	}
	
	/**
	* set Yaml
	* @param	string yaml formated
	* @return 	string yaml formated
	*/
	public function dumpYaml($value)
	{
		$this->yaml = new Dumper();
		return $this->yaml->dump($value);
	}
	/**
	 * Replace new-line with html break new-line
	 * @return	string
	 */
	public function getFormattedHelp()
	{
		return str_replace("\n", "<br />\n", $this->getHelp());
	}
	
	public function getDisplayType( \Busybee\DatabaseBundle\Entity\EnumeratorRepository $enum )
	{
		$types = array();
		foreach($this->getTypes($enum) as $w)
			$types = array_merge($types, $w);
		if (in_array($this->getType(), $types))
			return array_search($this->getType(), $types);
		if (0 === strpos($this->getType(), 'enum_'))
			return 'Enumerator ('.ucwords(str_replace(array('enum_', '_'), array('', ' '), $this->getType())).')';
		return $this->getType();
	}
	/**
	 * get Types
	 * 
	 * @param	\Busybee\DatabaseBundle\Entity\EnumeratorRepository
	 * @return	array	Field Type Definitions
	 */
	public function getTypes( \Busybee\DatabaseBundle\Entity\EnumeratorRepository $enum )
	{
		$types = array(
				'Alpha-Numeric' => array(
					'Short String <= 255 Characters' 		=> 'string',
					'Long String > 255 Characters'			=> 'Symfony\Component\Form\Extension\Core\Type\TextType',
				),
				'Numbers' => array( 
					'Integer' 								=> 'integer',
					'Number'								=> 'float',
				),
				'Date - Time' => array(
					'Date'									=> 'date',
					'Time'									=> 'time',
					'Date / Time'							=> 'datetime',
					'Year'									=> 'year',
				),
				'Specials' => array(
					'Yes / No'								=> 'yesno',
					'Array'									=> 'array',
					'Object'								=> 'object',
					'Child Link'							=> 'child',
					'Parent Link'							=> 'parent',
				),
			)
		;
		$query = $enum->createQueryBuilder('e');
		$result = $query->select('e.name')	
			->groupBy('e.name')
			->orderBy('e.name')	
			->getQuery()
			->getResult();
		if (is_array($result)) {
			$types['Enumerated'] = array();
			foreach ($result as $w) 
				$types['Enumerated'][$w['name']] = 'enum_'.$w['name'];
		}
		return $types ;
	}
	/**
	 * set Was
	 *
	 * @param	array	Was
	 * @return	\Busybee\DatabaseBundle\Entity\Field
	 */
	public function setWas($was)
	{
		$this->was = $was;

		return $this;
	}
	/**
	 * get Was
	 *
	 * @return	array	Was
	 */
	public function getWas()
	{
		return $this->was;
	}
}
