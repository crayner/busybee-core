<?php

namespace Busybee\InstituteBundle\Events;

use Busybee\InstituteBundle\Model\YearManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class YearSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * YearSubscriber constructor.
     * @param YearManager $manager
     */
    public function __construct(YearManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $event = $this->manager->preSubmit($event);
    }
}