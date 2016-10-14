<?php

namespace Busybee\RecordBundle\Model ;

class ParentManager extends ElementManager
{
	public $class = 'Busybee\\RecordBundle\\Entity\\ParentType';
	
	private $link ;
		
	public function add()
	{
		$this->link = $this->container->get('record.link.manager');

		$parameters = $this->field->parseYaml($this->field->getParameters());
		
		$trans = $this->container->get('translator');
		
		$this->link->setParent(new \STDClass);
		
		if (isset($parameters['table']))
			$this->link->setParentValue('table', $parameters['table']);
		else
		 	throw new \InvalidArgumentException('The parent must define a table name in the parameters.');

		if (isset($parameters['data']))
			$this->link->setParentValue('data', $parameters['data']);
		else
		 	$this->link->setParentValue('data', 'record_id');

		if (isset($parameters['display']))
			$this->link->setParentValue('display', $parameters['display']);
		else
		 	throw new \InvalidArgumentException('The parent must define a display field in the parameters.');

		if (isset($parameters['relationship']))
			$this->link->setParentValue('relationship', $parameters['relationship']);
		else
		 	throw new \InvalidArgumentException('The parent must define a relationship in the parameters.');
	
		$choices =  $this->getChoices();
		$display = $parameters['display'];
		if (is_array($display))
			$display = implode(',', $display);
		$settings = array(
					'label'					=> $this->field->getPrompt(),
					'required'				=> false,
					'data'					=> $this->getValue(),
					'render_optional_text'	=> false,
					'multiple'				=> false,
					'choices'				=> $choices,
					'placeholder'			=> $trans->trans('record.parent.placeholder.select', array('%field%' => $display), 'BusybeeRecordBundle'),
					'empty_data'			=> NULL,
				)
			;
		if (empty($choices))
			$settings['help_block'] = $trans->trans('record.parent.placeholder.empty', array('%field%' => $parameters['data'], '%table%' => $parameters['table']), 'BusybeeRecordBundle');			
		$settings = $this->getHelp($settings);
		$this->form->add(strtolower($this->field->getName()), 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', $settings);
		return $this->form;
	}
	
	public function getValue()
	{
		$string = $this->container->get('record.parent.repository');
		return $this->getElementValue($string);	
	}

	public function save() 
	{
		$record = $this->container->get('record.parent.repository')->findOneByRecordField($this->rec_id, $this->field->getId());
		$record = $this->saveElement($record, $this->getData());
		return ;
	}
	
	public function getChoices()
	{
		return $this->container->get('record.list.manager')->getList($this->link->getParentTable('table'), $this->link->getParentData('data'), $this->link->getParentDisplay('display'));	
	}
	
}
