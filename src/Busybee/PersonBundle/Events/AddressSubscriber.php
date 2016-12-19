<?php

namespace Busybee\PersonBundle\Events ;

use Busybee\InstituteBundle\Repository\CampusResourceRepository;
use Busybee\PersonBundle\Entity\Phone;
use Busybee\PersonBundle\Repository\PhoneRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddressSubscriber implements EventSubscriberInterface
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

        if (empty($data['streetNumber']) && intval($data['streetName']) > 0)
        {
            $num = intval($data['streetName']);
            $data['streetNumber'] = $num;
            $data['streetName'] = trim(str_replace($num, '', $data['streetName']));
        }

        $event->setData($data);
        dump($data);
dump($form);

    }
}