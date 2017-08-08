<?php
namespace Busybee\TimeTableBundle\Entity;

use Busybee\TimeTableBundle\Model\PeriodActivityModel;

/**
 * PeriodActivity
 */
class PeriodActivity extends PeriodActivityModel
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $lastModified;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var \Busybee\InstituteBundle\Entity\Space
     */
    private $space;

    /**
     * @var \Busybee\StaffBundle\Entity\Staff
     */
    private $tutor1;

    /**
     * @var \Busybee\StaffBundle\Entity\Staff
     */
    private $tutor2;

    /**
     * @var \Busybee\StaffBundle\Entity\Staff
     */
    private $tutor3;

    /**
     * @var \Busybee\ActivityBundle\Entity\Activity
     */
    private $activity;

    /**
     * @var \Busybee\TimeTableBundle\Entity\Period
     */
    private $period;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $createdBy;

    /**
     * @var \Busybee\SecurityBundle\Entity\User
     */
    private $modifiedBy;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get lastModified
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Set lastModified
     *
     * @param \DateTime $lastModified
     *
     * @return PeriodActivity
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return PeriodActivity
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get space
     *
     * @return \Busybee\InstituteBundle\Entity\Space
     */
    public function getSpace()
    {
        if ($this->getLocal())
            return $this->getLocalSpace();

        if (empty($this->space) && !empty($this->getActivity()))
            $this->setspace($this->getActivity()->getspace());
        if (empty($this->space) && !empty($this->getActivity()) && !empty($this->getActivity()->getTutor1()))
            $this->setspace($this->getActivity()->getTutor1()->getHomeroom());

        return $this->space;
    }

    /**
     * Set space
     *
     * @param \Busybee\InstituteBundle\Entity\Space $space
     *
     * @return PeriodActivity
     */
    public function setSpace(\Busybee\InstituteBundle\Entity\Space $space = null)
    {
        $this->space = $space;

        return $this;
    }

    /**
     * Get activity
     *
     * @return \Busybee\ActivityBundle\Entity\Activity
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Set activity
     *
     * @param \Busybee\ActivityBundle\Entity\Activity $activity
     * @param   boolean $add
     *
     * @return PeriodActivity
     */
    public function setActivity(\Busybee\ActivityBundle\Entity\Activity $activity = null, $add = true)
    {
        if ($add)
            $activity->addPeriod($this);

        $this->activity = $activity;

        return $this;
    }

    /**
     * Get tutor1
     *
     * @return \Busybee\StaffBundle\Entity\Staff
     */
    public function getTutor1()
    {
        if ($this->getLocal())
            return $this->getLocalTutor1();

        if (empty($this->tutor1) && !empty($this->getActivity()))
            $this->setTutor1($this->getActivity()->getTutor1());

        if (empty($this->tutor1) && !empty($this->getActivity())) {
            if (!empty($this->getActivity()->getSpace())) {
                $this->setTutor1($this->getActivity()->getSpace()->getStaff());
            }
        }

        return $this->tutor1;
    }

    /**
     * Set tutor1
     *
     * @param \Busybee\StaffBundle\Entity\Staff $tutor1
     *
     * @return PeriodActivity
     */
    public function setTutor1(\Busybee\StaffBundle\Entity\Staff $tutor1 = null)
    {
        $this->tutor1 = $tutor1;

        return $this;
    }

    /**
     * Get tutor2
     *
     * @return \Busybee\StaffBundle\Entity\Staff
     */
    public function getTutor2()
    {
        if ($this->getLocal())
            return $this->getLocalTutor2();

        if (empty($this->tutor2) && !empty($this->getActivity()))
            $this->setTutor2($this->getActivity()->getTutor2());

        return $this->tutor2;
    }

    /**
     * Set tutor2
     *
     * @param \Busybee\StaffBundle\Entity\Staff $tutor2
     *
     * @return PeriodActivity
     */
    public function setTutor2(\Busybee\StaffBundle\Entity\Staff $tutor2 = null)
    {
        $this->tutor2 = $tutor2;

        return $this;
    }

    /**
     * Get tutor3
     *
     * @return \Busybee\StaffBundle\Entity\Staff
     */
    public function getTutor3()
    {
        if ($this->getLocal())
            return $this->getLocalTutor3();

        if (empty($this->tutor3) && !empty($this->getActivity()))
            $this->setTutor3($this->getActivity()->getTutor3());

        return $this->tutor3;
    }

    /**
     * Set tutor3
     *
     * @param \Busybee\StaffBundle\Entity\Staff $tutor3
     *
     * @return PeriodActivity
     */
    public function setTutor3(\Busybee\StaffBundle\Entity\Staff $tutor3 = null)
    {
        $this->tutor3 = $tutor3;

        return $this;
    }

    /**
     * Get period
     *
     * @return \Busybee\TimeTableBundle\Entity\Period
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set period
     *
     * @param \Busybee\TimeTableBundle\Entity\Period $period
     * @param   boolean $add
     *
     * @return PeriodActivity
     */
    public function setPeriod(\Busybee\TimeTableBundle\Entity\Period $period = null, $add = true)
    {
        if ($add)
            $period->addActivity($this);

        $this->period = $period;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdBy
     *
     * @param \Busybee\SecurityBundle\Entity\User $createdBy
     *
     * @return PeriodActivity
     */
    public function setCreatedBy(\Busybee\SecurityBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return \Busybee\SecurityBundle\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set modifiedBy
     *
     * @param \Busybee\SecurityBundle\Entity\User $modifiedBy
     *
     * @return PeriodActivity
     */
    public function setModifiedBy(\Busybee\SecurityBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }
}
