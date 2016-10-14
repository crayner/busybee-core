<?php

namespace Busybee\RecordBundle\Model ;

class StringManager extends ElementManager
{
	public $class = 'Busybee\\RecordBundle\\Entity\\StringType';
	
	public function add()
	{
		$settings = array(
					'label'					=> $this->field->getPrompt(),
					'required'				=> false,
					'data'					=> $this->getValue(),
					'render_optional_text'	=> false,
				)
			;
		$settings = $this->getHelp($settings);
		$this->form->add(strtolower($this->field->getName()), 'Symfony\Component\Form\Extension\Core\Type\TextType', $settings);
		return $this->form;
	}
	
	public function getValue()
	{
		$string = $this->container->get('record.string.repository');
		return $this->getElementValue($string);	
	}

	public function save() 
	{
		$record = $this->container->get('record.string.repository')->findOneByRecordField($this->rec_id, $this->field->getId());
		$record = $this->saveElement($record, $this->getData());
		return ;
	}
	

}
