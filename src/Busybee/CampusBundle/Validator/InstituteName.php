<?php
namespace Busybee\CampusBundle\Validator ;

use Symfony\Component\Validator\Constraint;

class InstituteName extends Constraint
{
    public $message = 'campus.error.institute.name';
	
	public function validatedBy()
	{
		return 'institute_name_validator' ; 
	}
	
	public function __construct()
	{
		parent::__construct();
		$this->addImplicitGroupName('Default');
		return $this ;
	}
}
