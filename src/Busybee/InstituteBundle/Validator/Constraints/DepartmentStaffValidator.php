<?php

namespace Busybee\InstituteBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase;

class DepartmentStaffValidator extends ConstraintValidatorBase
{

    public function validate($value, Constraint $constraint)
    {

        if (empty($value))
            return;

        $s = [];
        foreach ($value->toArray() as $entity) {
            $s[$entity->getStaff()->getFormatName()] = empty($s[$entity->getStaff()->getFormatName()]) ? 1 : intval($s[$entity->getStaff()->getFormatName()]) + 1;
        }

        foreach ($s as $q => $w)
            if ($w > 1)
                $this->context->buildViolation('department.staff.uniquestaff', ['%name%' => $q])
                    ->addViolation();

    }
}