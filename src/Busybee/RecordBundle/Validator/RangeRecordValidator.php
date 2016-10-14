<?php

namespace Busybee\RecordBundle\Validator ;

use Symfony\Component\Form\FormError ;

class RangeRecordValidator extends RecordValidator
{
	private $name = 'range';
	
	public function validate($data)
	{
        if (null === $data || '' === $data) 
            return true;

		$range = $this->getRange();
		
		$errMsg = sprintf('The constraints are not consistent with the limit validator. Min = %s, Max = %s', strval($range['min']), strval($range['max']));
		
		if (count($range) > 2) 
			throw new \InvalidArgumentException($errMsg);
		if ($range['max'] < $range['min'])
			throw new \InvalidArgumentException($errMsg);
		if ($data < $range['min'])
			return false;
		if ($data > $range['max'])
			return false;
		return true;
	}
	/**
	 * @return form
	 */
	public function setErrorMessage($form, $tableName, $fieldName, $value)
	{
		$length = $this->getRange();
		$parameters = array('%value%' => $value, '%name%' => $this->displayFieldName($fieldName), '%min%' => $length['min'], '%max%' => $length['max'], '%given%' => intval($value));
		$message = $this->getMessage('record.not_valid.range.default', $parameters);
		$form->get(strtolower($fieldName))->addError(new FormError($message));
		return $form ;
	}
	
	private function getRange()
	{
		$constraints = $this->getConstraints();
		if (is_array($constraints))
		{
			if (! empty($this->get('min')))
				$constraints['min'] = $this->get('min');
			if (! empty($this->get('max')))
				$constraints['max'] = $this->get('max');
		}
		elseif (! is_array($constraints) and intval($constraints) > 0)
		{
			$x = intval($constraints);
			$constraints = array();
			$constraints['max'] = $x;
		}
		$length = array();
		if (is_array($constraints))
		{
			$length['min'] = 0;
			if (isset($constraints['min']))
				$length['min'] = intval($constraints['min']);
			if (isset($constraints['max']))
				$length['max'] = intval($constraints['max']);
		}
		return $length;
	}
}