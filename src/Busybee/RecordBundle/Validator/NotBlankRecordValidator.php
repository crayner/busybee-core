<?php

namespace Busybee\RecordBundle\Validator ;

use Symfony\Component\Form\FormError ;

class NotBlankRecordValidator extends RecordValidator
{
	private $name = 'notblank';
	
	public function validate($data)
	{
        if (null === $data || '' === $data) 
            return false;
		return true;
	}
	/**
	 * @return form
	 */
	public function setErrorMessage($form, $tableName, $fieldName, $value)
	{
		$parameters = array('%value%' => $value, '%name%' => $this->displayFieldName($fieldName));
		$message = $this->getMessage('record.not_valid.notblank.default', $parameters);
		$form->get(strtolower($fieldName))->addError(new FormError($message));
		return $form ;
	}

	
}