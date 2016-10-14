<?php

namespace Busybee\DatabaseBundle\Model ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser ;
use Symfony\Component\Yaml\Dumper ;

class FieldManager
{
	private $container ;
	private $context ;
	private $fields ;
	private $table ;
	private $refresh ;
	private $logger ;
	
	public function __construct( Container $container)
	{
		$this->container = $container ;
		$this->context = array('File'=>__FILE__);
		$this->logger = $this->container->get('monolog.logger.busybee');

		return $this;
	}
	

	public function manageFields($config, $fields, $table, $refresh)
	{
		$this->table = $table ;
		$this->fields = $fields ;
		$this->refresh = $refresh ;;
		$x = count($this->fields);
		foreach ($config as $w){
			$field = $w['field'];
			$this->fields[$x] = new \Busybee\DatabaseBundle\Entity\Field();
			$this->fields[$x]->setTable($this->table);
			foreach ($field as $name => $value)
			{
				switch (strtolower($name))
				{
					case 'name':
						$this->fields[$x]->setName($value);
						break;
					case 'type':
						$this->fields[$x]->setType($value);
						break;
					case 'validator':
						$this->fields[$x]->setValidator($value);
						break;
					case 'prompt':
						$this->fields[$x]->setPrompt($value);
						break;
					case 'help':
						$this->fields[$x]->setHelp($value);
						break;
					case 'parameters':
						$this->fields[$x]->setParameters($value);
						break;
					case 'sortkey':
						$this->fields[$x]->setSortkey($value);
						break;
					case 'was':
						$this->fields[$x]->setWas($value);
						break;
					case 'role':
						$r = $this->container->get('security.role.repository')->findOneBy(array('role' => $value));
						if ($r instanceof \Busybee\SecurityBundle\Entity\Role)
							$this->fields[$x]->setRole($r);
						break;
					default:
						throw new \InvalidArgumentException(sprintf('What to do with field-%s?', $name));
				}
			}
			$x++;
		}
		return $this ;
	}

	public function saveFields($changed)
	{
		if (empty($this->fields))
			return ;
		$context = $this->context;
		$context['METHOD'] = __METHOD__;
		$em = $this->container->get('doctrine')->getManager();
		$fr = $this->container->get('field.repository');
		$tr = $this->container->get('table.repository');
		$flush = false;
		$tableName = NULL;
		foreach ( $this->fields as $q => $field )
		{
			if ($tableName !== $field->getSelectTable()->getName()) 
			{
				$tableName = $field->getSelectTable()->getName();
				$table = $tr->findOneBy(array('name' => $tableName));
			}
			$exists = $fr->findOneByNameTableWas($field, $table);
			$save = false;
			if (! empty($exists)) 
			{
				if ($save = $this->testFieldChanges($exists, $field))
				{
					$exists->setName($field->getName());
					$exists->setType($field->getType());
					$exists->setRole($field->getSelectRole());
					$exists->setSortkey($field->getSortkey());
					$exists->setValidator($field->getValidator());
					$exists->setParameters($field->getParameters());
					$exists->setHelp($field->getHelp());
					$exists->setPrompt($field->getPrompt());
					$exists->setTable($table);
				}
				$field = $exists;
			} 
			if (($this->refresh and $save) or intval($field->getID()) === 0)
			{
				$field->setTable($table);
				$em->persist($field);
				$flush = true;
				$this->messages[] = array('status' => 'success', 
					'message' => $this->container->get('translator')->trans('database.load.saved.field', array(
						'%name%' => $field->getDisplayName(), 
						'%table%' => $table->getName()
					),
				'BusybeeDatabaseBundle'));
				$context['LINE'] = intval(__LINE__) + 1;
				$this->logger->info(
					$this->container->get('translator')->trans(
						'database.load.saved.field', 
						array(
							'%name%' => $field->getDisplayName(), 
							'%table%' => $table->getName()
						),
						'BusybeeDatabaseBundle'),
					$context);
			}
			$this->fields[$q] = $field;
		}
		if ($flush) {
			$em->flush();
			$changed = true;
		}
		return $changed;
	}
		
	private function testFieldChanges($old, $new)
	{
		if ($old->getType() !== $new->getType())
			return true;

		if ($old->getName() !== $new->getName())
			return true;

		if ($old->getValidator() !== $new->getValidator())
			return true;

		if ($old->getParameters() !== $new->getParameters())
			return true;

		if ($old->getSortkey() !== $new->getSortkey())
			return true;

		if ($old->getPrompt() !== $new->getPrompt())
			return true;

		if ($old->getHelp() !== $new->getHelp())
			return true;

		$oldR = $old->getSelectTable();
		$newR = $new->getSelectTable();
		if (! empty($oldR))
			$oldR = $oldR->getName();
		if (! empty($newR))
			$newR = $newR->getName();
		if ($oldR !== $newR)
			return true;

		$oldR = $old->getSelectRole();
		$newR = $new->getSelectRole();
		if (! empty($oldR))
			$oldR = $oldR->getRole();
		if (! empty($newR))
			$newR = $newR->getRole();
		if ($oldR !== $newR)
			return true;

		return false;
	}
	
	/**
	 * get Fields
	 *
	 * @return 	array of \Busybee\DatabaseBundle\Entity\Field
	 */
	public function getFields()
	{
		return $this->fields;
	}
}