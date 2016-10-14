<?php

namespace Busybee\RecordBundle\Validator ;

use Symfony\Component\Form\FormError ;

class UpperRecordValidator extends RecordValidator
{

	public function validate($data)
	{
        if (null === $data || '' === $data) 
            return true;

		$pattern = '[a-z]';
		if (intval(preg_match('@'.$pattern.'@', $data)) === 0)
			return true;
		return false;
	}
	/**
	 * @return form
	 */
	public function setErrorMessage($form, $tableName, $fieldName, $value)
	{
		$parameters = array('%value%' => $value, '%name%' => $this->displayFieldName($fieldName), '%table%' => $tableName);
		$message = $this->getMessage('record.not_valid.upper.default', $parameters);
		$form->get(strtolower($fieldName))->addError(new FormError($message));
		return $form ;
	}
	
	public function format($data)
	{
		return strtoupper($data);
	}
}