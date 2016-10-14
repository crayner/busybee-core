<?php

namespace Busybee\RecordBundle\Model ;

use Symfony\Component\Yaml\Parser ;
use Symfony\Component\Form\FormError ;

class EnumManager extends ElementManager
{
	public $type;
	public $class = 'Busybee\\RecordBundle\\Entity\\EnumType';
	
	public function add()
	{
		$this->type = str_replace(array('enum_', '_'), array('', ' '), $this->field->getType());
		$choices = array();
		$query = $this->container->get('enumerator.repository')->createQueryBuilder('a');
		$results = $query->select(array('a.value','a.prompt'))
			->orderBy('a.prompt')
			->where('a.name = :name')
			->setParameter('name', $this->type)
			->getQuery()
			->getResult();
		foreach($results as $w)
			if (0 === strpos($w['prompt'], 'SubArray:')) 
				$choices[$w['value']] = $this->parseYaml($w['prompt']);
			else
				$choices[$w['value']] = $w['prompt'];
		$settings = array(
					'label'					=> $this->field->getPrompt(),
					'required'				=> false,
					'data'					=> $this->getValue(),
					'render_optional_text'	=> false,
					'choices'				=> $choices,
				)
			;
		$settings = $this->getHelp($settings);
		$settings = $this->getPlaceholder($settings);
		$this->form->add(strtolower($this->field->getName()), 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', $settings);
		if (empty($choices))
		{
			$message = $this->container->get('translator')->trans('record.element.enum.data_missing', array('%name%' => $this->type), 'BusybeeRecordBundle');
			$this->form->get(strtolower($this->field->getName()))->addError(new FormError($message));
		}	
		return $this->form;
	}

	public function getValue()
	{
		$repo = $this->container->get('record.enum.repository');
		return $this->getElementValue($repo);	
	}

	public function save() 
	{
		$record = $this->container->get('record.enum.repository')->findOneByRecordField($this->rec_id, $this->field->getId());
		$record = $this->saveElement($record, $this->getData());
		return ;
	}

	/**
	 * get Yaml
	 * @param	string 
	 * @return 	array
	 */
	 private function parseYaml($value)
	 {
		 $this->yaml = new Parser();
		 return $this->yaml->parse(str_replace('SubArray:', '', $value));
	 }


}
