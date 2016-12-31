<?php
namespace Busybee\FormBundle\Validator ;

use Symfony\Component\Validator\Constraints\Choice;

class SettingChoice extends Choice
{
    public $name ;
    public $strict = true ;
    public $choices = array();

	public function validatedBy()
	{
		return 'setting_validator' ;
	}
}
