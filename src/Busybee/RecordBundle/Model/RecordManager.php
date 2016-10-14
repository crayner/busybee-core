<?php

namespace Busybee\RecordBundle\Model ;

class RecordManager extends ElementManager
{
	public $class = 'Busybee\\RecordBundle\\Entity\\Record';
	protected $rec_id;
	protected $table;
	
	public function add()
	{
		$this->form->add('record', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
					'data'			=> intval($this->rec_id),
				)
			)
		;
		return $this->form;
	}
	
/*	private function getStringValue()
	{
		$string = $this->container->get('record.string.repository');
		return $this->getValue($string);	
	}
*/
	public function setRecord($rec_id, $table)
	{
		if ($table->getLimits() === 'single')
			$rec_id = 1;
		if (intval($rec_id) === 0) 
			$this->rec_id = $this->container->get('record.repository')->getNextRecord($table);
		else
			$this->rec_id = $rec_id;
		$this->table = $table;
		return $this->rec_id;
	}
	
	public function getValue()
	{
		return $this->rec_id;
	}

	public function save()
	{
		$record = $this->container->get('record.repository')->findByRecordTable($this->rec_id, $this->table->getId());	
		if (! empty($record))
			return false;
		$record = $this->container->get('record.repository')->createNew();
		$record->setRecord($this->rec_id);
		$record->setTable($this->table->getId());
		$record->setUser($this->container->get('security.token_storage')->getToken()->getUser()->getId());
		$entityManager = $this->container->get('doctrine')->getManager();
		$entityManager->persist($record);
		$entityManager->flush();
		return true ;
	}
}
