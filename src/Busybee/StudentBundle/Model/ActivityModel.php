<?php
namespace Busybee\StudentBundle\Model;

use Busybee\StudentBundle\Entity\Activity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

abstract class ActivityModel
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
        $this->count = 0;
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
     * @return Activity
     */
    public function incCount()
    {
        $this->count = intval($this->count) < 1 ? 1 : $this->count + 1;

        return $this;
    }

    /**
     * @return string
     */
    public function getActivityCount()
    {
        if (intval($this->getTeachingLoad()) === 0)
            return '( ' . intval($this->count) . ' )';

        return '( ' . intval($this->count) . ' of ' . $this->getTeachingLoad() . ' )';
    }

    /**
     * @return string
     */
    public function getAlert()
    {
        if (!empty($this->alert))
            return $this->alert;

        if ($this->getTeachingLoad() > 0 && $this->getTeachingLoad() !== $this->getCount())
            $this->alert = 'alert-warning';

        if ($this->getTeachingLoad() > 0 && $this->getCount() > $this->getTeachingLoad())
            $this->alert = 'alert-danger';

        if ($this->getTeachingLoad() > 0 && $this->getCount() === $this->getTeachingLoad())
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
        return $this->count;
    }
}