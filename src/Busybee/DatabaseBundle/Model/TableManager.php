<?php

namespace Busybee\DatabaseBundle\Model ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Yaml\Parser ;
use Symfony\Component\Yaml\Dumper ;

class TableManager
{
	private $container ;
	private $context ;
	private $fieldManager ;
	private $refresh ;
	private $logger ;
	
	public function __construct( Container $container)
	{
		$this->container = $container ;
		$this->context = array('File'=>__FILE__);
		$this->logger = $this->container->get('monolog.logger.busybee');

		return $this;
	}
	
	public function manageTables($config, $refresh)
	{
		$this->tables = array();
		$this->fields = array();
		$this->refresh = $refresh ;
		$x = 0;
		foreach($config as $table)
		{
			$this->table = new \Busybee\DatabaseBundle\Entity\Table();
			foreach($table['table'] as $name => $value)
			{
				switch (strtolower($name))
				{
					case 'name':
						$this->table->setName($value);
						break;
					case 'fields':
						$this->fieldManager = $this->container->get('database.field.manager')->manageFields($value, $this->fields, $this->table, $this->refresh);
						$this->fields = $this->fieldManager->getFields();
						break;
					case 'limits':
						$this->table->setLimits($value);
						break;
					case 'role':
						$r = $this->container->get('security.role.repository')->findOneBy(array('role' => $value));
						if ($r instanceof \Busybee\SecurityBundle\Entity\Role)
							$this->table->setRole($r);
						break;
					case 'parent':
						$r = $this->container->get('table.repository')->findOneBy(array('name' => $value));
						if ($r instanceof \Busybee\DatabaseBundle\Entity\Table)
							$this->table->setParent($r);
						break;
					case 'links':
						$this->table->setLinkDetails($value);
						break;
					default:
						throw new \InvalidArgumentException(sprintf('What to do with table-%s?', $name));
				}
			}
			$this->tables[$x] = $this->table ;
			unset($this->table);
			$x++; 
		}
		return $this;
	}		

	public function saveTables($changed)
	{
		
		$context = $this->context;
		$context['METHOD'] = __METHOD__;
		if (empty($this->tables))
			return ;
		$em = $this->container->get('doctrine')->getManager();
		$tr = $this->container->get('table.repository');
		$flush = false;
		foreach ( $this->tables as $q => $table )
		{
			$exists = $tr->findOneBy(array('name' => $table->getName()));
			$save = false;
			if (! empty($exists)) 
			{
				if ($save = $this->testTableChanges($exists, $table))
				{
					$exists->setLimits($table->getLimits());
					$exists->setRole($table->getRole());
					$exists->setParent($table->getParent());
				}
				$table = $exists;
			} 
			if (($this->refresh and $save) or intval($table->getID()) === 0)
			{
				$em->persist($table);
				$flush = true;
				$this->messages[] = array('status' => 'success', 'message' => $this->container->get('translator')->trans('database.load.saved.table', array('%name%' => $table->getName()), 'BusybeeDatabaseBundle'));
				$context['LINE'] = intval(__LINE__) + 1;
				$this->logger->info(
					$this->container->get('translator')->trans(
						'database.load.saved.table', 
						array(
							'%name%' => $table->getName(),
						),
						'BusybeeDatabaseBundle'),
					$context);
			}
			$this->tables[$q] = $table;
		}
		if ($flush) {
			$em->flush();
			$changed = true;
		}
		return $changed;
	}
	
	private function testTableChanges($old, $new)
	{
		if ($old->getLimits() !== $new->getLimits())
			return true;

		$oldR = $old->getSelectRole();
		$newR = $new->getSelectRole();
		if (! empty($oldR))
			$oldR = $oldR->getRole();
		else 
			$oldR = NULL;
		if (! empty($newR))
			$newR = $newR->getRole();
		else 
			$newR = NULL;
		if ($oldR !== $newR)
			return true;

		$oldR = $old->getSelectParent();
		$newR = $new->getSelectParent();
		if (! empty($oldR))
			$oldR = $oldR->getName();
		else
			$oldR = NULL;
		if (! empty($newR))
			$newR = $newR->getName();
		else
			$newR = NULL;
		if ($oldR !== $newR)
			return true;

		if ($old->getLinkDetails() !== $new->getLinkDetails())
			return true;


		return false;
	}
	
	public function saveFields($changed)
	{
		return $this->fieldManager->saveFields($changed);
	}
}