<?php

namespace Busybee\Program\GradeBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Grades extends Constraint
{
	public $message = 'student.grades.error';

	public function validatedBy()
	{
		return 'student_grades_validator';
	}
}
