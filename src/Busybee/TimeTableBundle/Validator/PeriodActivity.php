<?php

namespace Busybee\TimeTableBundle\Validator;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PeriodActivity
{
    /**
     * @return string
     */
    public function validate($period, ExecutionContextInterface $context, $payload)
    {
        $activities = $period->getActivities();
        $ok = null;
        $x = new ArrayCollection();
        foreach ($activities as $q => $act) {
            if ($x->contains($act->getActivity()))
                $ok = $q;
            else
                $x->add($act->getActivity());
        }
        if (!is_null($ok)) {
            $context->buildViolation('period.activity.unique')
                ->atPath('activity')
                ->addViolation();
            return;
        }
        return;
    }
}
