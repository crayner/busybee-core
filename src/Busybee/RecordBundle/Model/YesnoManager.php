<?php

namespace Busybee\RecordBundle\Model ;

class YesnoManager extends ElementManager
{
	public $class = 'Busybee\\RecordBundle\\Entity\\YesnoType';
	
	public function add()
	{
		$yesno = array();
		$parameters = $this->field->parseYaml($this->field->getParameters());
		if (isset($parameters['yes']) && isset($parameters['yes']['key']) && isset($parameters['yes']['prompt']))
			$yesno[$parameters['yes']['prompt']] = $parameters['yes']['key'];
		else 
			throw new \InvalidArgumentException(sprintf('The Yes No field for %s has not correctly defined a Yes definition.', $this->field->getName()));

		if (isset($parameters['no']) && isset($parameters['no']['key']) && isset($parameters['no']['prompt']))
			$yesno[$parameters['no']['prompt']] = $parameters['no']['key'];
		else 
			throw new \InvalidArgumentException(sprintf('The Yes No field for %s has not correctly defined a No definition.', $this->field->getName()));

		$settings = array(
					'label'					=> $this->field->getPrompt(),
					'required'				=> false,
					'data'					=> $this->getValue(),
					'render_optional_text'	=> false,
					'choices'				=> $yesno,
				)
			;
		$settings = $this->getHelp($settings);
		$settings = $this->getPlaceholder($settings);
		$this->form->add(strtolower($this->field->getName()), 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', $settings);
		return $this->form;
	}
	
	public function getValue()
	{
		$string = $this->container->get('record.yesno.repository');
		return $this->getElementValue($string);	
	}

	public function save() 
	{
		$record = $this->container->get('record.yesno.repository')->findOneByRecordField($this->rec_id, $this->field->getId());
		$record = $this->saveElement($record, $this->getData());
		return ;
	}
}
