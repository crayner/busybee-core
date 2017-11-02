<?php

namespace Busybee\TimeTableBundle\Events;

use Busybee\Facility\InstituteBundle\Entity\Space;
use Busybee\People\StaffBundle\Entity\Staff;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PeriodActivitySubscriber implements EventSubscriberInterface
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

        $activity = $form->get('activity')->getData();

        if (!is_null($activity->getTutor1()) && intval($activity->getTutor1()->getId()) === intval($data['tutor1']))
            $data['tutor1'] = '';
        if (!is_null($activity->getTutor2()) && intval($activity->getTutor2()->getId()) === intval($data['tutor2']))
            $data['tutor2'] = '';
        if (!is_null($activity->getTutor3()) && intval($activity->getTutor3()->getId()) === intval($data['tutor3']))
            $data['tutor3'] = '';
        if (!is_null($activity->getSpace()) && intval($activity->getSpace()->getId()) === intval($data['space']))
            $data['space'] = '';

        $event->setData($data);
    }
}