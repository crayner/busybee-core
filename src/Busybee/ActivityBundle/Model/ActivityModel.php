<?php

namespace Busybee\ActivityBundle\Model;

use Busybee\ActivityBundle\Entity\Activity;
use Busybee\TimeTableBundle\Model\ActivityInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

abstract class ActivityModel implements ActivityInterface
{
    /**
     * @var integer
     */
    private $count;

    /**
     * @var string
     */
    private $alert;

    /**
     * ActivityModel constructor.
     */
    public function __construct()
    {
        $this->setCount(0);
    }

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
        return $this->getName() . ' (' . $this->getNameShort() . ')';
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

    /**
     * @return string
     */
    public function getAlert()
    {
        if (!empty($this->alert))
            return $this->alert;

        if ($this->getTeachingLoad() < 1)
            return $this->alert = '';

        if ($this->getTeachingLoad() !== $this->getCount())
            $this->alert = 'alert-warning';

        if ($this->getCount() > $this->getTeachingLoad())
            $this->alert = 'alert-danger';

        if ($this->getCount() === $this->getTeachingLoad())
            $this->alert = 'alert-success';

        return $this->alert;
    }

    /**
     * @return Activity
     */
    public function setAlert($alert)
    {
        $this->alert = $alert;

        return $this;
    }

    /**
     * @return integer
     */
    public function getCount()
    {
        return intval($this->count);
    }

    /**
     * Set Count
     *
     * @param $count
     * @return Activity
     */
    public function setCount($count)
    {
        $this->count = intval($count);

        return $this;
    }

    /**
     * Can Delete
     * @todo Added can delete logic
     */
    public function canDelete()
    {
        return true;
    }
}