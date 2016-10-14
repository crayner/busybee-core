<?php

namespace Busybee\RecordBundle\Validator ;

use Symfony\Component\DependencyInjection\ContainerInterface as Container ;

interface RecordValidatorInterface
{
	public function __construct(Container $container, array $constraints);
	
	public function validate($data);

	public function setErrorMessage($form, $tableName, $fieldName, $value);

	public function format($data);
}