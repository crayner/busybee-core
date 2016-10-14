<?php

namespace Busybee\RecordBundle\Model ;

class YearManager extends ElementManager
{
	public $class = 'Busybee\\RecordBundle\\Entity\\YearType';
	
	public function add()
	{
		$years = array();
		$parameters = $this->field->parseYaml($this->field->getParameters());
		$start = -100;
		$finish = 5;
		if (isset($parameters['start']))
			$start = $parameters['start'];
		if (isset($parameters['finish']))
			$finish = $parameters['finish'];
		for ($y=$start; $y<=$finish; $y++)
			$years[date('Y', strtotime($y.' Years'))] = date('Y', strtotime($y.' Years'));
		$settings = array(
					'label'					=> $this->field->getPrompt(),
					'required'				=> false,
					'data'					=> $this->getValue(),
					'render_optional_text'	=> false,
					'choices'				=> $years,
				)
			;
		$settings = $this->getHelp($settings);
		$this->form->add(strtolower($this->field->getName()), 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', $settings);
		return $this->form;
	}
	
	public function getValue()
	{
		$repo = $this->container->get('record.year.repository');
		return strval($this->getElementValue($repo));	
	}

	public function save() 
	{
		$record = $this->container->get('record.year.repository')->findOneByRecordField($this->rec_id, $this->field->getId());
		$record = $this->saveElement($record, $this->getData());
		return ;
	}
}
