<?php

namespace Busybee\RecordBundle\Model ;

class ChildManager extends ElementManager
{
	public $class = 'Busybee\\RecordBundle\\Entity\\ChildType';
	
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
		$this->form->add(strtolower($this->field->getName()), 'Symfony\Component\Form\Extension\Core\Type\HiddenType', $settings);
		return $this->form;
	}
	
	public function getValue()
	{
		$string = $this->container->get('record.child.repository');
		return intval($this->getElementValue($string));	
	}

	public function save() 
	{
		$this->setValidRecord(true);
		return ;
		$record = $this->container->get('record.child.repository')->findOneByRecordField($this->rec_id, $this->field->getId());
		$record = $this->saveElement($record, $this->getData());
		return ;
	}
	

}
