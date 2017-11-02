<?php

namespace Busybee\Facility\InstituteBundle\Validator;

use Symfony\Component\Validator\Constraint;

class InstituteName extends Constraint
{
	public $message = 'campus.error.institute.name';

	public function __construct()
	{
		parent::__construct();
		$this->addImplicitGroupName('Default');

		return $this;
	}

	public function validatedBy()
	{
		return 'institute_name_validator';
	}
}
