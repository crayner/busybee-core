<?php
namespace Busybee\HomeBundle\Validator ;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Yaml extends Constraint
{
    public $message = 'array.error';

}