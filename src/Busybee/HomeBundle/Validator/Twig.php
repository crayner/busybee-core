<?php
namespace Busybee\HomeBundle\Validator ;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Twig extends Constraint
{
    public $message = 'twig.error';
}