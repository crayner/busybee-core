<?php

namespace Busybee\TimeTableBundle\Validator\Constraints;

use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class ColumnsValidator extends ConstraintValidator
{
    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * PeriodsValidator constructor.
     * @param SettingManager $sm
     */
    public function __construct(SettingManager $sm)
    {
        $this->sm = $sm;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (empty($value))
            return;

        $open = new \DateTime('1970-01-01 ' . $this->sm->get('SchoolDay.Open'));
        $close = new \DateTime('1970-01-01 ' . $this->sm->get('SchoolDay.Close'));

        foreach ($value as $q => $column) {

            if ($open > $column->getStart()) {
                $this->context->buildViolation($constraint->message . '.open')
                    ->atPath('[' . $q . '].start')
                    ->setParameter('%open%', $open->format('H:i'))
                    ->addViolation();
            }

            if ($close < $column->getEnd()) {
                $this->context->buildViolation($constraint->message . '.close')
                    ->atPath('[' . $q . '].end')
                    ->setParameter('%close%', $close->format('H:i'))
                    ->addViolation();
            }

            if ($column->getStart() > $column->getEnd()) {
                $this->context->buildViolation($constraint->message . '.order')
                    ->atPath('[' . $q . '].start')
                    ->addViolation();
            }
        }
        return;
    }
}