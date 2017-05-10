<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\InstituteBundle\Entity\SpecialDay;
use Busybee\InstituteBundle\Entity\Term;
use Busybee\SecurityBundle\Doctrine\UserManager;
use Busybee\SecurityBundle\Entity\User;
use Busybee\SystemBundle\Setting\SettingManager;
use Busybee\TimeTableBundle\Entity\Column;
use Busybee\TimeTableBundle\Entity\Day;
use Busybee\TimeTableBundle\Entity\TimeTable;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class TimeTableManager
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var \Busybee\InstituteBundle\Entity\Year
     */
    private $year;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var SettingManager
     */
    private $sm;

    /**
     * @var TimeTable
     */
    private $tt;

    /**
     * @var array
     */
    private $specialDays;

    /**
     * @var array
     */
    private $terms;

    /**
     * @var array
     */
    private $pointer;
    /**
     * @var array
     */
    private $columns;
    /**
     * @var array
     */
    private $schoolDays;

    /**
     * TimeTableManager constructor.
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage, UserManager $um, ObjectManager $om, SettingManager $sm)
    {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->year = $um->getSystemYear($this->user);
        $this->om = $om;
        $this->sm = $sm;
        $this->tt = $this->om->getRepository(TimeTable::class)->createQueryBuilder('t')
            ->leftJoin('t.year', 'y')
            ->where('y.id = :year_id')
            ->setParameter('year_id', $this->year->getId())
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @return array
     */
    public function getTerms()
    {
        if (!empty($this->terms))
            return $this->terms;
        $this->terms = $this->om->getRepository(Term::class)->createQueryBuilder('t')
            ->leftJoin('t.year', 'y')
            ->where('y.id = :year_id')
            ->setParameter('year_id', $this->year->getId())
            ->orderBy('t.firstDay', 'ASC')
            ->getQuery()
            ->getResult();
        return $this->terms;
    }

    public function getDays($key)
    {
        $term = $this->terms[$key];
        $first = $term->getFirstDay();
        $addDay = new \DateInterval('P1D');
        $schoolDays = $this->sm->get('schoolWeek');
        $days = [];
        $this->getSpecialDays();
        while ($first <= $term->getLastDay()) {
            if (in_array($first->format('D'), $schoolDays)) {
                $day = new \stdClass();
                $day->date = clone $first;
                $day->skip = false;
                if (!$this->tt->getSpecialDaySkip() && in_array($first, $this->specialDays))
                    $day->skip = true;
                $days[] = $day;
            }
            $first->add($addDay);
        }

        $this->getTimeTableDays();

        $days = $this->mapDays($days);

        return $days;
    }

    public function getSpecialDays()
    {
        if (!empty($this->specialDays))
            return $this->specialDays;

        $days = $this->om->getRepository(SpecialDay::class)->createQueryBuilder('s')
            ->leftJoin('s.year', 'y')
            ->where('y.id = :year_id')
            ->setParameter('year_id', $this->year->getId())
            ->orderBy('s.day', 'ASC')
            ->getQuery()
            ->getResult();
        $this->specialDays = [];

        foreach ($days as $day)
            $this->specialDays[] = $day->getDay();

        return $this->specialDays;

    }

    /**
     * Get Timetable Days
     */
    private function getTimeTableDays()
    {
        $this->columns = [];

        $this->tt->getColumns()->count();
        foreach ($this->tt->getColumns()->toArray() as $key => $col) {
            if ($col->getMappingInfo() == 'Rotate') {
                $this->columns['rotate'][$col->getNameShort()] = $key;
            } else {
                $this->columns['fixed'][$col->getNameShort()] = $key;
            }
        }

        $this->schoolDays = [];

        $this->tt->getDays()->count();
        foreach ($this->tt->getDays()->toArray() as $key => $day)
            if ($day->getDayType())
                $this->schoolDays[strtoupper($day->getName())] = 'rotate';
            else
                $this->schoolDays[strtoupper($day->getName())] = 'fixed';


    }

    /**
     * @param $days
     * @return mixed
     */
    private function mapDays($days)
    {
        $col = reset($this->columns['rotate']);
        foreach ($days as $q => $day) {
            $code = strtoupper($day->date->format('D'));
            if ($this->schoolDays[$code] == 'rotate') {
                $day->ttday = $this->tt->getColumns()->get($col);

                $col = next($this->columns['rotate']);
                if (false === $col)
                    $col = reset($this->columns['rotate']);

            } else {
                $day->ttday = $this->tt->getColumns()->get($this->columns['fixed'][$code]);
            }
            $days[$q] = $day;
        }
        return $days;
    }
}