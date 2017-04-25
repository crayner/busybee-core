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
//        $form = $event->getForm();
//        $year = $form->getData();

        if (!empty($data['grades'])) {
            $seq = 0;
            foreach ($data['grades'] as $q => $w) {
                $w['sequence'] = ++$seq;

                $data['grades'][$q] = $w;
            }
        }

//        $form->setData($year);
        $event->setData($data);
    }
}