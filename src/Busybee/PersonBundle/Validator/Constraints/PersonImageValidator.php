<?php
namespace Busybee\PersonBundle\Validator\Constraints ;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraint ;
use Symfony\Component\Validator\Constraints\ImageValidator ;


class PersonImageValidator extends ImageValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
dump($value);
        if (empty($value))
            return ;

        if ($value instanceof File && empty($value->getFilename()))
            return ;

		parent::validate($value, $constraint);
    }
}