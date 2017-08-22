<?php

namespace Busybee\Core\HomeBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Twig extends Constraint
{
	public $message = 'twig.error';
}