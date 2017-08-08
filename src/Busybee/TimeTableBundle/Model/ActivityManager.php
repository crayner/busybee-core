<?php

namespace Busybee\TimeTableBundle\Model;

use Busybee\InstituteBundle\Entity\Year;
use Busybee\ActivityBundle\Entity\Activity;
use Busybee\ActivityBundle\Model\ActivityManager as ActivityManagerBase;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ActivityManager extends ActivityManagerBase
{
    /**
     * @var array
     */
    private $activityCount;

    /**
     * ActivityManager constructor.
     *
     * @param ObjectManager $om
     * @param FlashBagInterface $flashbag
     * @param TranslatorInterface $trans
     */
    public function __construct(ObjectManager $om, FlashBagInterface $flashbag, TranslatorInterface $trans, Year $year)
    {
        parent::__construct($om, $flashbag, $trans);
        $this->year = $year;
        $this->activityCount = [];
    }

    public function getAlert(Activity $activity): string
    {
        if (!$activity instanceof Activity)
            return '';
        if ($activity->getCount() === 0 || $activity->getAlert() === '')
            $this->getActivityCount($activity);

        return $activity->getAlert();
    }

    public function getActivityCount(Activity $activity): int
    {
        if (!$activity instanceof Activity)
            return 0;
        if (isset($this->activityCount[$activity->getId()]))
            return $this->activityCount[$activity->getId()];

        $result = $this->getObjectManager()->getRepository(Activity::class)->createQueryBuilder('a')
            ->leftJoin('a.periods', 'p')
            ->select('COUNT(p.id)')
            ->where('a.id = :act_id')
            ->setParameter('act_id', $activity->getId())
            ->getQuery()
            ->getSingleScalarResult();

        $activity
            ->setCount(intval($result))
            ->getAlert();

        $this->activityCount[$activity->getId()] = intval($result);
        return $result;
    }
}