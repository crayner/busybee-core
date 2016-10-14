<?php

namespace Busybee\RecordBundle\Validator ;

use Symfony\Component\Form\FormError ;

class RegexRecordValidator extends RecordValidator
{
	private $name = 'regex';

	public function validate($data)
	{
        if (null === $data || '' === $data) 
            return true;

		$pattern = $this->getPattern();
		if (intval(preg_match($pattern, $data)) === 0)
			return false;
		return true;
	}
	/**
	 * @return form
	 */
	public function setErrorMessage($form, $tableName, $fieldName, $value)
	{
		$parameters = array('%value%' => $value, '%name%' => $this->displayFieldName($fieldName), '%pattern%' => $this->getPattern());
		$message = $this->getMessage('record.not_valid.regex.default', $parameters);
		$form->get(strtolower($fieldName))->addError(new FormError($message));
		return $form ;

	}
	
	public function setName($name) 
	{
		$this->name = strtolower($name);
		
		return $this;
	}

	private function getPattern()
	{
		$pattern = 	$this->get('0');
		if (empty($pattern))
			$pattern = $this->get('pattern');
		if (is_array($pattern))
			$pattern = $pattern['pattern'];
		return $pattern;
	}
}