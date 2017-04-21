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
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (!empty($data['grades'])) {
            $seq = 0;
            foreach ($data['grades'] as $q => $w) {
                $w['sequence'] = ++$seq;

                $data['grades'][$q] = $w;
            }
        }

        $event->setData($data);
    }
}