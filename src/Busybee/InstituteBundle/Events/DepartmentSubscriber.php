<?php

namespace Busybee\InstituteBundle\Events;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DepartmentSubscriber implements EventSubscriberInterface
{
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
        $data = $event->getData();


        if (is_array($data['courses'])) {
            $items = [];
            foreach ($data['courses'] as $q => $w)
                if (!in_array($w, $items))
                    $items[$q] = $w;
            $data['courses'] = $items;
        }
        if (is_array($data['staff'])) {
            $items = [];
            foreach ($data['staff'] as $q => $w)
                if (!in_array($w['staff'], $items))
                    $items[$q] = $w['staff'];
                else
                    unset($data['staff'][$q]);
        }

        $event->setData($data);
    }
}