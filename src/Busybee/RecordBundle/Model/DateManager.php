<?php

namespace Busybee\RecordBundle\Model ;

class DateManager extends ElementManager
{
	public $class = 'Busybee\\RecordBundle\\Entity\\DateType';
	
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
			$years[] = date('Y', strtotime($y.' Years'));
		$settings = array(
					'label'					=> $this->field->getPrompt(),
					'required'				=> false,
					'data'					=> $this->getValue(),
					'render_optional_text'	=> false,
					'years'					=> $years,
				)
			;
		if (! empty($parameters['type']) and $parameters['type'] === 'single_text')
		{
			$settings['widget']	=	'single_text';
			$settings['format']	=	'yyyy-MM-dd';
		}
		$settings = $this->getHelp($settings);
		$this->form->add(strtolower($this->field->getName()), 'Symfony\Component\Form\Extension\Core\Type\DateType', $settings);
		return $this->form;
	}
	
	public function getValue()
	{
		$repo = $this->container->get('record.date.repository');
		return $this->getElementValue($repo);	
	}

	public function save() 
	{
		$record = $this->container->get('record.date.repository')->findOneByRecordField($this->rec_id, $this->field->getId());
		$record = $this->saveElement($record, $this->getData());
		return ;
	}
}
