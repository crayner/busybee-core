<?php

namespace Busybee\StudentBundle\Model;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ActivityModel
{
    /**
     * @return string
     */
    public function getNameYear()
    {
        return '(' . $this->getYear()->getName() . ') ' . $this->getName();
    }

    public function studentNumbersValidate(ExecutionContextInterface $context, $payload)
    {
        if (empty($this->getspace()) || empty($this->getSpace()->getCapacity()))
            return;

        if ($this->getStudentCount() > $this->getSpace()->getCapacity()) {
            $context->buildViolation('activity.space.overload')
                ->atPath('space')
                ->setParameter('%capacity%', $this->getSpace()->getCapacity())
                ->setParameter('%studentCount%', $this->getStudentCount())
                ->addViolation();
        }
    }

    /**
     * @return integer
     */
    public function getStudentCount()
    {
        return $this->getStudents()->count();
    }

    public function getFullName()
    {
        return $this->getName() . '(' . $this->getNameShort() . ') ' . $this->getYear()->getName();
    }

}