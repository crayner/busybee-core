<?php

namespace Busybee\StaffBundle\Events;

use Busybee\InstituteBundle\Entity\Space;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StaffSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * StaffSubscriber constructor.
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_submit
        // event and that the preSubmit method should be called.
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $staff = $form->getData();
        $space_id = empty($staff->getHomeroom()) ? null : $staff->getHomeroom()->getId();

        if (intval($space_id) != intval($data['homeroom'])) {
            $space = $staff->getHomeRoom();
            if (!$space instanceof Space && !empty($data['homeroom']))
                $space = $this->om->getRepository(Space::class)->find($data['homeroom']);
            if (!$space instanceof Space)
                $space = null;
            if ($space instanceof Space && empty($data['homeroom'])) {
                $space->setStaff(null);
                $staff->setHomeroom(null);
            }
            if (empty($data['homeroom'])) {
                $staff->setHomeroom(null);
                $space->setStaff(null);
            } else {
                $space->setStaff($staff);
                $staff->setHomeroom($space);
            }
            if (!is_null($space)) {
                $this->om->persist($space);
                $this->om->flush();
            }
            $form->setData($staff);
        }
    }
}