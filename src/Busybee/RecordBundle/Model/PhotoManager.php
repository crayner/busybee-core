<?php

namespace Busybee\RecordBundle\Model ;

class PhotoManager extends ElementManager
{
	public $class = 'Busybee\\RecordBundle\\Entity\\PhotoType';
	
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
		$this->form->add(strtolower($this->field->getName()), 'Symfony\Component\Form\Extension\Core\Type\FileType', $settings);
		return $this->form;
	}
	
	public function getValue()
	{
		$string = $this->container->get('record.photo.repository');
		return $this->getElementValue($string);	
	}

	public function save() 
	{
		$record = $this->container->get('record.photo.repository')->findOneByRecordField($this->rec_id, $this->field->getId());
		$record = $this->saveElement($record, $this->getData());
		return ;
	}
	

}
