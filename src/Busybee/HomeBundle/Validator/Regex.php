<?php
namespace Busybee\HomeBundle\Validator ;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Regex extends Constraint
{
    public $message = 'regex.error';

}