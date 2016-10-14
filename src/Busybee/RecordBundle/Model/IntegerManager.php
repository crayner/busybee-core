<?php

namespace Busybee\RecordBundle\Model ;

class IntegerManager extends ElementManager
{
	public $class = 'Busybee\\RecordBundle\\Entity\\IntegerType';
	
	public function add()
	{
		
		$settings = array(
					'label'					=> $this->field->getPrompt(),
					'required'				=> false,
					'data'					=> $this->getValue(),
					'render_optional_text'	=> false,
					'scale'					=> 0,
				)
			;
		$settings = $this->getHelp($settings);
		$this->form->add(strtolower($this->field->getName()), 'Symfony\Component\Form\Extension\Core\Type\NumberType', $settings);
		return $this->form;
	}
	
	public function getValue()
	{
		$string = $this->container->get('record.integer.repository');
		return $this->getElementValue($string);	
	}

	public function save() 
	{
		$record = $this->container->get('record.integer.repository')->findOneByRecordField($this->rec_id, $this->field->getId());
		$record = $this->saveElement($record, $this->getData());
		return ;
	}
	
	
	public function getElementValue($repo)
	{
		return intval(parent::getElementValue($repo));
	}
}
