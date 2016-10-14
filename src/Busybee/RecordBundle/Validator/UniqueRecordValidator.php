<?php

namespace Busybee\RecordBundle\Validator ;

use Symfony\Component\Form\FormError ;

class UniqueRecordValidator extends RecordValidator
{

	public function validate($data)
	{
        if (null === $data || '' === $data)
            return true;
		
		$type = $this->field->getType();
		if (strpos($type, 'enum_') === 0)
			$type = 'enum';
		
		$em = $this->container->get('record.'.strtolower($type).'.repository');
		$query = $em->createQueryBuilder('r');
		$result = $query	->select('r.record')
							->where('r.record != :rec_id')
							->andwhere('r.field = :field_id')
							->andwhere('r.value = :value')
							->setParameter('rec_id', $this->rec_id)
							->setParameter('field_id', $this->field->getId())
							->setParameter('value', $data)
							->getQuery()
							->getResult();
		
		if (empty($result))
			return true;
		return false;;
	}
	/**
	 * @return form
	 */
	public function setErrorMessage($form, $tableName, $fieldName, $value)
	{
		$parameters = array('%value%' => $value, '%name%' => $this->displayFieldName($fieldName), '%table%' => $tableName);
		$message = $this->getMessage('record.not_valid.unique', $parameters);
		$form->get(strtolower($fieldName))->addError(new FormError($message));
		return $form ;
	}
}