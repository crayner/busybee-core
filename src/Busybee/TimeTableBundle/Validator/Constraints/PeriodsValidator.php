<?php

namespace Busybee\TimeTableBundle\Validator\Constraints;

use Busybee\SystemBundle\Setting\SettingManager;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class PeriodsValidator extends ConstraintValidator
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

        $overlap = false;
        $order = false;
        $break = false;
        $error = false;

        $iterator = $value->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getStart() < $b->getStart()) ? -1 : 1;
        });

        $column = $value->getOwner();

        $value = new ArrayCollection(iterator_to_array($iterator, false));

        $data = $value->toArray();
        $x = reset($data);
        $start = $x->getStart();
        $end = $x->getEnd();
        $keep = clone $x;
        $q = 0;
        while (!$error && false !== ($day = next($data))) {
            if (!$error) {
                if ($day->getStart() < $end) {
                    $error = true;
                    $overlap = true;
                    $keep = clone $day;
                }
                if ($day->getStart() >= $day->getEnd()) {
                    $error = true;
                    $order = true;
                    $keep = clone $day;
                }
                if ($day->getStart() > $end) {
                    $error = true;
                    $break = true;
                    $keep = clone $day;
                }

                $end = $day->getEnd();
                $q++;
            }
        }


        if ($order) {
            $this->context->buildViolation($constraint->message['order'])
                ->atPath('[' . $q . '].start')
                ->addViolation();
        }
        if ($overlap) {
            $this->context->buildViolation($constraint->message['overlap'])
                ->atPath('[' . $q . '].start')
                ->setParameter('%end%', $keep->getEnd()->format('H:i'))
                ->addViolation();
        }
        if ($break) {
            $this->context->buildViolation($constraint->message['break'])
                ->atPath('[' . $q . '].start')
                ->setParameter('%end%', $keep->getEnd()->format('H:i'))
                ->addViolation();
        }
        if ($start < $column->getStart()) {
            $this->context->buildViolation($constraint->message['early'])
                ->atPath('[0].start')
                ->setParameter('%limit%', $column->getStart()->format('H:i'))
                ->addViolation();
        }
        if ($end > $column->getEnd()) {
            $this->context->buildViolation($constraint->message['late'])
                ->atPath('[' . ($value->count() - 1) . '].end')
                ->setParameter('%limit%', $column->getEnd()->format('H:i'))
                ->addViolation();
        }
        if ($end < $column->getEnd()) {
            $this->context->buildViolation($constraint->message['complete'])
                ->atPath('[' . ($value->count() - 1) . '].end')
                ->setParameter('%end%', $column->getEnd()->format('H:i'))
                ->setParameter('%start%', $column->getStart()->format('H:i'))
                ->addViolation();
        }
        if ($start > $column->getStart()) {
            $this->context->buildViolation($constraint->message['complete'])
                ->atPath('[0].start')
                ->setParameter('%end%', $column->getEnd()->format('H:i'))
                ->setParameter('%start%', $column->getStart()->format('H:i'))
                ->addViolation();
        }

        return $value;
    }
}