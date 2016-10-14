<?php
// src/General/ValidationBundle/Validator/Constraints/Phone.php
namespace General\ValidationBundle\Validator\Constraints ;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Phone extends Constraint
{
    public $message = 'The phone number needs to be formated correctly: "%format%" for "%value%"';

    public function validatedBy()
    {
        return 'phone.validator';
    }    
    
}