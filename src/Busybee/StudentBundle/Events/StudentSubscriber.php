<?php

namespace Busybee\StudentBundle\Events;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class StudentSubscriber implements EventSubscriberInterface
{
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
        $student = $form->getData();
        if (is_null($student)) return;

        if (is_null($data['citizenship1PassportScan'])) $data['citizenship1PassportScan'] = $student->getCitizenship1PassportScan();
        if (is_null($data['nationalIDCardScan'])) $data['nationalIDCardScan'] = $student->getNationalIDCardScan();

        $event->setData($data);
    }
}