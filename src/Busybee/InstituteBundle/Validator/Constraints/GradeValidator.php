<?php

namespace Busybee\InstituteBundle\Validator\Constraints;

use Busybee\InstituteBundle\Entity\Grade;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator as ConstraintValidatorBase;

class GradeValidator extends ConstraintValidatorBase
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * GradeValidator constructor.
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->om = $objectManager;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return;

        $years = $this->om->getRepository(Grade::class)->findBy(['year' => $constraint->year->getId()], ['sequence' => 'ASC']);

        if (!empty($years))
            foreach ($years as $y) {
                if (!$value->contains($y))
                    if (!$y->canDelete()) {
                        $this->context->buildViolation('year.grade.error.delete', ['%grade%' => $y->getGrade()])
                            ->addViolation();
                        return;
                    }
            }

        $test = [];
        foreach ($value as $grade)
            $test[$grade->getGrade()] = isset($test[$grade->getGrade()]) ? $test[$grade->getGrade()] + 1 : 1;

        foreach ($test as $w)
            if ($w > 1) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
                return;

            }
    }
}