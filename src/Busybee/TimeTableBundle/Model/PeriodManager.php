<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var Translator
     */
    private $translator;

    /**
     * @var array
     */
    private $failedStatus;

    /**
     * @var array
     */
    private $spaces;

    /**
     * @var array
     */
    private $staff;

    /**
     * PeriodManager constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, TranslatorInterface $translator, $id = null)
    {
        $this->om = $om;
        $this->pr = $om->getRepository(Period::class);
        $this->period = $this->injectPeriod($id);
        $this->status = new \stdClass();
        $this->translator = $translator;
        $this->failedStatus = [];
        $this->spaces = [];
        $this->staff = [];
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
            $status->message = '';
            return $status;
        }
        if (isset($this->status->id) && $this->status->id === $activity->getId())
            return $this->status;

        $this->status = new \stdClass();
        $this->status->id = $activity->getId();
        $this->status->class = '';
        $this->status->message = '';
        $this->status->message = '';

        if (is_null($activity->getSpace())) {
            $this->status->class = ' alert-warning';
            $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.space.missing', [], 'BusybeeTimeTableBundle');
        } else {
            if (isset($this->spaces[$activity->getSpace()->getName()])) {
                $this->status->class = ' alert-warning';
                $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.space.duplicate', ['%space%' => $activity->getSpace()->getName()], 'BusybeeTimeTableBundle');
            }
            $this->spaces[$activity->getSpace()->getName()] = $activity->getSpace()->getName();

            if (!is_null($activity->getSpace()->getStaff()) && isset($this->staff[$activity->getSpace()->getStaff()->getId()])) {
                $this->status->class = ' alert-warning';
                $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.duplicate', [], 'BusybeeTimeTableBundle');
            }
            if (!is_null($activity->getSpace()->getStaff()))
                $this->staff[$activity->getSpace()->getStaff()->getId()] = $activity->getSpace()->getStaff()->getFullName();
        }

        if (is_null($activity->getTutor1())) {
            $this->status->class = ' alert-warning';
            $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.missing', [], 'BusybeeTimeTableBundle');
        } else {
            if (!is_null($activity->getTutor1()) && isset($this->staff[$activity->getTutor1()->getId()])) {
                $this->status->class = ' alert-warning';
                $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor1()->getFullName()], 'BusybeeTimeTableBundle');
            }
            if (!is_null($activity->getTutor1()))
                $this->staff[$activity->getTutor1()->getId()] = $activity->getTutor1()->getFullName();

            if (!is_null($activity->getTutor2()) && isset($this->staff[$activity->getTutor2()->getId()])) {
                $this->status->class = ' alert-warning';
                $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor2()->getFullName()], 'BusybeeTimeTableBundle');
            }
            if (!is_null($activity->getTutor2()))
                $this->staff[$activity->getTutor2()->getId()] = $activity->getTutor2()->getFullName();

            if (!is_null($activity->getTutor3()) && isset($this->staff[$activity->getTutor3()->getId()])) {
                $this->status->class = ' alert-warning';
                $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor3()->getFullName()], 'BusybeeTimeTableBundle');
            }
            if (!is_null($activity->getTutor3()))
                $this->staff[$activity->getTutor3()->getId()] = $activity->getTutor3()->getFullName();
        }

        if (!empty($this->status->message)) {
            $this->failedStatus[$this->status->id] = $this->status->class;
            $this->status->message = trim($this->status->message);
        }
        return $this->status;
    }

    /**
     * @param PeriodActivity|null $activity
     * @return array
     */
    public function getActivityDetails(PeriodActivity $activity = null)
    {
        if (!$activity instanceof PeriodActivity) {
            $data = [];
            $data['%space%'] = '';
            $data['%tutor1%'] = '';
            $data['%tutor2%'] = '';
            $data['%tutor3%'] = '';
            return $data;
        }

        $data = [];
        $data['%space%'] = is_null($activity->getSpace()) ? '' : $activity->getSpace()->getName();
        $data['%tutor1%'] = is_null($activity->getTutor1()) ? '' : $activity->getTutor1()->getFullName();
        $data['%tutor1%'] = is_null($activity->getSpace()) || is_null($activity->getSpace()->getStaff()) ? $data['%tutor1%'] : $activity->getSpace()->getStaff()->getFullName();
        $data['%tutor2%'] = is_null($activity->getTutor2()) ? '' : $activity->getTutor2()->getFullName();
        $data['%tutor3%'] = is_null($activity->getTutor3()) ? '' : $activity->getTutor3()->getFullName();
        return $data;
    }

    public function getFailedCount()
    {
        return count($this->failedStatus);
    }
}