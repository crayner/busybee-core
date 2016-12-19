<?php

namespace Busybee\PersonBundle\Events ;

use Busybee\InstituteBundle\Repository\CampusResourceRepository;
use Busybee\PersonBundle\Entity\Phone;
use Busybee\PersonBundle\Repository\PhoneRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SUBMIT => 'preSubmit');
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        unset($data['address1_list'], $data['address2_list']);

        $event->setData($data);
    }
}