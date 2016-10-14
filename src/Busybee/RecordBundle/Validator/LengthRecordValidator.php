<?php

namespace Busybee\RecordBundle\Validator ;

use Symfony\Component\Form\FormError ;

class LengthRecordValidator extends RecordValidator
{
	private $name = 'length';
	
	public function validate($data)
	{
        if (null === $data || '' === $data) 
            return true;

		$length = $this->getLength();
		
		$errMsg = sprintf('The constraints are not consistent with the limit validator. Min = %s, Max = %s', strval($length['min']), strval($length['max']));
		
		if (count($length) > 2) 
			throw new \InvalidArgumentException($errMsg);
		if ($length['max'] < $length['min'])
			throw new \InvalidArgumentException($errMsg);
		if (mb_strlen($data) < $length['min'])
			return false;
		if (mb_strlen($data) > $length['max'])
			return false;
		return true;
	}
	/**
	 * @return form
	 */
	public function setErrorMessage($form, $tableName, $fieldName, $value)
	{
		$length = $this->getLength();
		$parameters = array('%value%' => $value, '%name%' => $this->displayFieldName($fieldName), '%min%' => $length['min'], '%max%' => $length['max'], '%given%' => mb_strlen($value));
		$message = $this->getMessage('record.not_valid.length.default', $parameters);
		$form->get(strtolower($fieldName))->addError(new FormError($message));
		return $form ;
	}
	
	private function getLength()
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