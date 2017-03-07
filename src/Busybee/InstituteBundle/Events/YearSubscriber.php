<?php

namespace Busybee\InstituteBundle\Events;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class YearSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SUBMIT => 'postSubmit');
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $year = $form->getData();
        if (isset($data['terms']) && is_array($data['terms'])) {
            foreach ($data['terms'] as $q => $w) {
                $w['year'] = $year->getId();
                $data['terms'][$q] = $w;
            }
        }
        if (isset($data['specialDays']) && is_array($data['specialDays'])) {
            foreach ($data['specialDays'] as $q => $w) {
                $w['year'] = $year->getId();
                $data['specialDays'][$q] = $w;
            }
        }


        dump($form->getData());
        dump($data);

        $event->setData($data);
    }
}