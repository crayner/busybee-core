<?php

namespace Busybee\RecordBundle\Validator ;

use Symfony\Component\Form\FormError ;

class EmailRecordValidator extends RecordValidator
{

	public function validate($data)
	{
        if (null === $data || '' === $data)
            return true;

		if (!preg_match('/^.+\@\S+\.\S+$/', $data)) 
			return false;
		
		$host = substr($data, strpos($data, '@') + 1);
        if (!$this->checkMX($host)) 
			return false;

		return true;
	}
	/**
	 * @return form
	 */
	public function setErrorMessage($form, $tableName, $fieldName, $value)
	{
		$parameters = array('%value%' => $value, '%name%' => $this->displayFieldName($fieldName));
		$message = $this->getMessage('record.not_valid.email', $parameters);
		$form->get(strtolower($fieldName))->addError(new FormError($message));
		return $form ;
	}
    /**
     * Check DNS Records for MX type.
     *
     * @param string $host Host
     *
     * @return bool
     */
    private function checkMX($host)
    {
        return checkdnsrr($host, 'MX');
    }

}