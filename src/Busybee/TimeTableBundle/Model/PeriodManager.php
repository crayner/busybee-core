<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Doctrine\Common\Persistence\ObjectManager;

class PeriodManager
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var \Busybee\TimeTableBundle\Repository\PeriodRepository
     */
    private $pr;

    /**
     * @var Period
     */
    private $period;

    /**
     * @var \stdClass
     */
    private $status;

    /**
     * PeriodManager constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, $id = null)
    {
        $this->om = $om;
        $this->pr = $om->getRepository(Period::class);
        $this->period = $this->injectPeriod($id);
        $this->status = new \stdClass();
    }

    /**
     * @param $id
     * @return PeriodManager
     */
    public function injectPeriod($id)
    {
        if ($id)
            $this->period = $this->pr->find($id);
        if (!$this->period instanceof Period)
            $this->period = new Period();
        return $this;
    }

    /**
     * @param $id
     * @return boolean
     */
    public function canDelete($id)
    {
        $period = $this->pr->find($id);
        return $period->canDelete();
    }

    /**
     * @param $activity
     * @return \stdClass
     */
    public function getActivityStatus(PeriodActivity $activity = null)
    {
        if (!$activity instanceof PeriodActivity) {
            $status = new \stdClass();
            $status->class = 'default';
            return $status;
        }
        if (isset($this->status->id) && $this->status->id === $activity->getId())
            return $this->status;
        $this->status = new \stdClass();
        $this->status->id = $activity->getId();
        $this->status->class = '';

        return $this->status;
    }
}