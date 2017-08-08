<?php

namespace Busybee\ActivityBundle\Model;

use Busybee\ActivityBundle\Entity\Activity;
use Busybee\TimeTableBundle\Entity\Line;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ActivityManager
{
    /**
     * @var OnjectManager
     */
    private $om;

    /**
     * @var FlashBagInterface
     */
    private $flashbag;

    /**
     * @var Activity
     */
    private $activity;

    /**
     * ActivityManager constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, FlashBagInterface $flashbag, TranslatorInterface $trans)
    {
        $this->om = $om;
        $this->flashbag = $flashbag;
        $this->trans = $trans;
    }

    public function deleteActivity($id)
    {
        $this->activity = $this->injectActivity($id);
        if (empty($id) || !$this->activity instanceof Activity) {
            $this->flashbag->add('warning', 'activity.delete.notfound');
            return;
        }

        if (!$this->canDelete($id, false)) {
            $this->flashbag->add('warning', 'activity.delete.locked');
            return;
        }

        try {
            $this->om->remove($this->activity);
            $this->om->flush();
        } catch (\Exception $e) {
            $this->flashbag->add('danger', $this->trans->trans('activity.delete.error', ['%error%' => $e->getMessage()], 'BusybeeStudentBundle'));
            return;
        }
        $this->activity = null;

        $this->flashbag->add('success', 'activity.delete.success');
        return;
    }

    /**
     * @param $id
     * @return Activity|null
     */
    public function injectActivity($id)
    {
        if ($id > 0 && $this->activity instanceof Activity && $id == $this->activity->getId())
            return $this->activity;

        $this->activity = null;

        if ($id > 0)
            $this->activity = $this->om->getRepository(Activity::class)->find($id);

        if ($this->activity instanceof Activity || is_null($this->activity))
            return $this->activity;

        return null;
    }

    /**
     * @param $id
     * @param bool $silent
     * @return bool
     */
    public function canDelete($id, $silent = true)
    {
        $this->activity = $this->injectActivity($id);

        $line = $this->om->getRepository(Line::class)->createQueryBuilder('l')
            ->leftJoin('l.activities', 'a')
            ->where('a.id = :act_id')
            ->setParameter('act_id', $this->activity->getId())
            ->getQuery()
            ->getResult();

        if (!empty($line)) {
            if (!$silent) {
                $this->flashbag->add('info', $this->trans->trans('activity.delete.line', ['%line%' => $line[0]->getFullName()], 'BusybeeStudentBundle'));
            }
            return false;
        }

        return $this->activity->canDelete();
    }

    /**
     * Get ObjectManager
     *
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        return $this->om;
    }
}