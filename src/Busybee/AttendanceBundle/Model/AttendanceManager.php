<?php

namespace Busybee\AttendanceBundle\Model;

use Busybee\ActivityBundle\Entity\Activity;
use Busybee\AttendanceBundle\Entity\AttendancePeriod;

class AttendanceManager
{
    /**
     * @var Activity
     */
    private $activity;

    /**
     * @var AttendancePeriod
     */
    private $attendPeriod;

    private $TTYear;

    /**
     * AttendanceManager constructor.
     * @param \stdClass $TTYear
     */
    public function __construct(\stdClass $TTYear)
    {
        $this->TTYear = $TTYear;

        return $this;
    }

    /**
     * @param AttendancePeriod|null $attendPeriod
     * @return $this
     */
    public function setAttendancePeriod(AttendancePeriod $attendPeriod = null)
    {
        $this->attendPeriod = $attendPeriod;

        return $this;
    }

    public function getAttendDates()
    {
        return [];
    }

    /**
     * @return Activity
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * @param Activity $activity
     * @return $this
     */
    public function setActivity(Activity $activity = null)
    {
        $this->activity = $activity;

        return $this;
    }
}