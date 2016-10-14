<?php

namespace Busybee\RecordBundle\Model ;

use Busybee\RecordBundle\Entity ;
use Busybee\RecordBundle\Validator\RegexRecordValidator ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Translation\Loader\YamlFileLoader ;
use Symfony\Component\HttpFoundation\Request ;

class FormManager
{
	private $container ; 
	private $entityManager ;
	private $form ;
	private $field ;
	private $table ;
	private $fields ;
	private $rec_id ;
	private $record ;
	private $data = array();
	private $user;
	private $newRecord ;
	private $changedRecord ;
	private $validRecord ;
	private $translator ;
	private $domain ;
	private $locale ;
	private $elementManager ; 
	
	public function __construct(Container $container)
	{
		$this->container = $container;
		$this->entityManager = $this->container->get('doctrine')->getManager();
		$this->translator = $this->container->get('translator');
		$this->translator->addLoader('yaml', new YamlFileLoader()) ;
		$this->domain = 'BusybeeRecordBundle';
		$this->locale = $this->container->get('session')->get('_locale');
		$this->translator->addResource('yaml', $this->domain.'.'.$this->locale.'.yml', $this->domain ) ;
		$this->elementManager = null;
		return $this;
	}
	
	public function injector( \Symfony\Component\Form\Form $form, $table, $fields, $rec_id = 0)
	{
		$this->form = $form ;
		$this->table = $table ;
		$this->fields = $fields ;
		$this->rec_id = $this->setRecord($rec_id)->getValue() ;
		$this->data = array();
		$this->user = null;
		return $this;
	}
	
	public function createMetaData()
	{
		$record = array();
		$record['title'] = $this->table->getName() . ' Record';
		$record['control'] = array();
		$record['control']['save'] = 'Save';
		$record['control']['save_and_add'] = 'Save & Add';
		$record['control']['delete'] = 'Delete';
		$record['control']['cancel'] = 'Cancel';
		$record['control']['_token'] = 'Token';
		return $record ;
	}
	
	public function buildForm()
	{
		foreach($this->fields as $this->field) 
		{
			$this->addElement();
		}
		return $this->form;
	}
	
	private function addElement()
	{
		$this->form = $this->getElementManager()->add();
	}
	
	private function getElementManager()
	{
		if (strpos(strtolower($this->field->getType()), 'enum_') === 0)
			$elementManager = 'EnumManager';
		else
			$elementManager = ucfirst($this->field->getType()).'Manager';
		$elementManager = '\\Busybee\\RecordBundle\\Model\\'.$elementManager;
		$this->ElementManager = new $elementManager($this->rec_id, $this->field, $this->table, $this->form, $this->container);
		return $this->ElementManager;
	}
	
	public function handleRequest(Request $request, array $fields)
	{
		$this->form->handleRequest($request);
		if ($this->form->isSubmitted()) {
			$this->data = array();
			$this->data['record'] = $this->form->get('record')->getData();
			$this->user = $this->container->get('security.token_storage')->getToken()->getUser();
			$this->rec_id = intval($this->data['record']);
			$this->changedRecord = false;
			$this->validRecord = true;
			foreach ($this->fields as $this->field) {

				$this->ElementManager = $this->getElementManager();
				$this->ElementManager->setData($this->form->get(strtolower($this->field->getName()))->getData());
				$this->ElementManager->save();

				if ($this->ElementManager->getChangedRecord())
					$this->changedRecord = true;
				$this->data[strtolower($this->field->getName())] = $this->ElementManager->getData();
				$this->form = $this->ElementManager->getForm();
				if (! $this->ElementManager->getValidElement())
					$this->validRecord = false;
			}
			if (! $this->validRecord) 
			{
				$this->container->get('session')->getFlashBag()->add(
					'danger',
					$this->translator->trans('record.save.failed', array(), 'BusybeeRecordBundle')
				);
			} 
			else
			{
				$this->container->get('session')->getFlashBag()->add(
					'success',
					$this->translator->trans('record.save.success', array(), 'BusybeeRecordBundle')
				);
			} 
			$this->record->save(NULL);
		}
		return $this->form;
	}
	
	private function setRecord($rec_id)
	{
		$this->record = new \Busybee\RecordBundle\Model\RecordManager($rec_id, $this->field, $this->table, $this->form, $this->container);
		$this->rec_id = $this->record->setRecord($rec_id, $this->table);
		$this->form = $this->record->add();
		return $this->record;
	}
}