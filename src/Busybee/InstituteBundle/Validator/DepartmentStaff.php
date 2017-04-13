<?php

namespace Busybee\InstituteBundle\Validator;

use Symfony\Component\Validator\Constraint;

class DepartmentStaff extends Constraint
{
    public $message = 'department.staff.invalid';

    public $errorPath;

    public function validatedBy()
    {
        return 'department_staff_validator';
    }
}
