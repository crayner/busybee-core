<?php
namespace Busybee\StudentBundle\Model;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

abstract class ActivityModel
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

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->getName() . ' (' . $this->getNameShort() . ') ' . $this->getYear()->getName();
    }

    /**
     * @return string
     */
    public function getGradeString()
    {
        $result = '';
        foreach ($this->getGrades() as $grade)
            $result .= $grade->getGrade() . ',';

        return trim($result, ',');
    }
}