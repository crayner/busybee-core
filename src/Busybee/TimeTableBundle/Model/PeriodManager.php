<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\InstituteBundle\Entity\Grade;
use Busybee\InstituteBundle\Entity\Space;
use Busybee\InstituteBundle\Entity\Year;
use Busybee\StaffBundle\Entity\Staff;
use Busybee\TimeTableBundle\Entity\ActivityGroups;
use Busybee\TimeTableBundle\Entity\Period;
use Busybee\TimeTableBundle\Entity\PeriodActivity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
     * @var array
     */
    private $students;

    /**
     * @var FlashBagInterface
     */
    private $flashbag;

    /**
     * @var Year
     */
    private $currentYear;

    /**
     * PeriodManager constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, TranslatorInterface $translator, FlashBagInterface $flashbag, Year $cy, $id = null)
    {
        $this->om = $om;
        $this->pr = $om->getRepository(Period::class);
        $this->period = $this->injectPeriod($id);
        $this->status = new \stdClass();
        $this->translator = $translator;
        $this->failedStatus = [];
        $this->spaces = [];
        $this->staff = [];
        $this->students = [];
        $this->flashbag = $flashbag;
        $this->currentYear = $cy;
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
                $act = $this->spaces[$activity->getSpace()->getName()];
                $this->status->class = ' alert-warning';
                $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.space.duplicate', ['%space%' => $activity->getSpace()->getName(), '%activity%' => $act->getFullName()], 'BusybeeTimeTableBundle');
            }
            $this->spaces[$activity->getSpace()->getName()] = $activity->getActivity();
        }

        if (is_null($activity->getTutor1())) {
            $this->status->class = ' alert-warning';
            $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.missing', [], 'BusybeeTimeTableBundle');
        } else {
            if (!is_null($activity->getTutor1()) && isset($this->staff[$activity->getTutor1()->getId()])) {
                $act = $this->staff[$activity->getTutor1()->getId()];
                $this->status->class = ' alert-warning';
                $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor1()->getFullName(), '%activity%' => $act->getFullName()], 'BusybeeTimeTableBundle');
            }
            if (!is_null($activity->getTutor1()))
                $this->staff[$activity->getTutor1()->getId()] = $activity->getActivity();

            if (!is_null($activity->getTutor2()) && isset($this->staff[$activity->getTutor2()->getId()])) {
                $act = $this->staff[$activity->getTutor2()->getId()];
                $this->status->class = ' alert-warning';
                $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor2()->getFullName(), '%activity%' => $act->getFullName()], 'BusybeeTimeTableBundle');
            }
            if (!is_null($activity->getTutor2()))
                $this->staff[$activity->getTutor2()->getId()] = $activity->getActivity();

            if (!is_null($activity->getTutor3()) && isset($this->staff[$activity->getTutor3()->getId()])) {
                $act = $this->staff[$activity->getTutor3()->getId()];
                $this->status->class = ' alert-warning';
                $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.staff.duplicate', ['%name%' => $activity->getTutor3()->getFullName(), '%activity%' => $act->getFullName()], 'BusybeeTimeTableBundle');
            }
            if (!is_null($activity->getTutor3()))
                $this->staff[$activity->getTutor3()->getId()] = $activity->getActivity();
        }

        if (!empty($this->status->message)) {
            $this->failedStatus[$this->status->id] = $this->status->class;
            $this->status->message .= ' ' . $this->translator->trans('period.activities.activity.report.button', [], 'BusybeeTimeTableBundle');
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
        $data['%tutor2%'] = is_null($activity->getTutor2()) ? '' : $activity->getTutor2()->getFullName();
        $data['%tutor3%'] = is_null($activity->getTutor3()) ? '' : $activity->getTutor3()->getFullName();
        return $data;
    }

    /**
     * @return int
     */
    public function getFailedCount()
    {
        return count($this->failedStatus);
    }

    /**
     * @param $id
     */
    public function generatePeriodReport($id)
    {
        $this->injectPeriod($id);

        if (!$this->period instanceof Period)
            throw new \InvalidArgumentException('The period has not been injected.');

        $result = $this->om->getRepository(Space::class)->findBy([], ['name' => 'ASC']);

        $spaces = new ArrayCollection();
        foreach ($result as $space)
            $spaces->add($space);

        $return = new \stdClass();

        $return->spaces = $this->removeUsedSpaces($spaces);


        $result = $this->om->getRepository(Staff::class)->findAll();

        $staff = new ArrayCollection();
        foreach ($result as $member)
            $staff->add($member);

        $iterator = $staff->getIterator();
        $iterator->uasort(function ($a, $b) {
            return ($a->getFullName() < $b->getFullName()) ? -1 : 1;
        });
        $staff = new ArrayCollection(iterator_to_array($iterator, false));

        $return->staff = $this->removeUsedStaff($staff);

        return $return;
    }

    /**
     * Remove Used Spaces
     *
     * @param $spaces
     * @return mixed
     */
    private function removeUsedSpaces($spaces)
    {
        $used = $this->getSpaces();
        foreach ($used as $space)
            if ($spaces->contains($space))
                $spaces->removeElement($space);

        return $spaces;
    }

    /**
     * Get Spaces
     *
     * @return ArrayCollection
     */
    private function getSpaces()
    {
        $spaces = new ArrayCollection();

        $acts = $this->period->getActivities();

        foreach ($acts as $act)
            if ($act->getSpace() instanceof Space and !$spaces->contains($act->getSpace()))
                $spaces->add($act->getSpace());

        return $spaces;
    }

    /**
     * Remove Used Staff
     *
     * @param $spaces
     * @return mixed
     */
    private function removeUsedStaff($staff)
    {
        $used = $this->getStaff();

        foreach ($used as $member)
            if ($staff->contains($member))
                $staff->removeElement($member);


        return $staff;
    }

    /**
     * Get Staff
     *
     * @return ArrayCollection
     */
    private function getStaff()
    {
        $staff = new ArrayCollection();

        $acts = $this->period->getActivities();

        foreach ($acts as $act) {
            dump($act->getTutor1());
            if ($act->getTutor1() instanceof Staff and !$staff->contains($act->getTutor1()))
                $staff->add($act->getTutor1());

            if ($act->getTutor2() instanceof Staff and !$staff->contains($act->getTutor2()))
                $staff->add($act->getTutor2());

            if ($act->getTutor3() instanceof Staff and !$staff->contains($act->getTutor3()))
                $staff->add($act->getTutor3());

        }

        return $staff;
    }

    /**
     * Get Period
     *
     * @return Period
     */
    public function getPeriod()
    {
        if (!$this->period instanceof Period)
            throw new \InvalidArgumentException('The period has not been injected.');
        return $this->period;
    }

    /**
     * @param $line
     */
    public function injectLineGroup($line)
    {
        $line = $this->om->getRepository(ActivityGroups::class)->find($line);

        $count = 0;

        $exists = new ArrayCollection();
        foreach ($this->period->getActivities() as $act)
            $exists->add($act->getActivity());

        foreach ($line->getActivities() as $activity)
            if (!$exists->contains($activity)) {
                $act = new PeriodActivity();
                $act->setPeriod($this->period);
                $act->setActivity($activity);
                $this->period->getActivities()->add($act);
                $count++;
            }

        if ($count > 0) {
            $this->flashbag->add('success', 'period.activities.line.added');
            $this->om->persist($this->period);
            $this->om->flush();
        } else
            $this->flashbag->add('warning', 'period.activities.line.none');

        return;

    }

    /**
     * @param $id
     */
    public function generateFullPeriodReport($id)
    {
        $this->injectPeriod($id);
        $data = new \stdClass();

        $rrr = $this->om->getRepository(Grade::class)->createQueryBuilder('g')
            ->leftJoin('g.year', 'y')
            ->leftJoin('g.students', 'i')
            ->where('y.id = :year_id')
            ->setParameter('year_id', $this->currentYear->getId())
            ->select('g')
            ->addSelect('i')
            ->andWhere('i.status IN (:status)')
            ->setParameter('status', ['Future', 'Current'], Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();
        $grades = [];
        foreach ($rrr as $grade) {
            $data->grades[$grade->getId()] = $grade;
            $students = [];
            foreach ($grade->getStudents() as $student)
                $students[$student->getStudent()->getId()] = $student->getStudent();
            $grades[$grade->getId()] = $students;
        }
        foreach ($this->period->getActivities() as $q => $pa) {
            $act = $pa->getActivity();
            foreach ($act->getStudents() as $student) {
                $grade = $student->getStudentGrade($this->currentYear);

                if (isset($grades[$grade->getId()][$student->getId()]))
                    unset($grades[$grade->getId()][$student->getId()]);
            }
        }

        foreach ($grades as $q => $grade) {
            if (!empty($grade)) {
                $grade = new ArrayCollection($grade);
                $iterator = $grade->getIterator();
                $iterator->uasort(function ($a, $b) {
                    return ($a->getFormatName(['surnameFirst' => true, 'preferredOnly' => false]) < $b->getFormatName(['surnameFirst' => true, 'preferredOnly' => false])) ? -1 : 1;
                });
                $grades[$q] = iterator_to_array($iterator, true);
            }
        }


        $data->missingStudents = $grades;
        dump($grades);
        return $data;
    }
}