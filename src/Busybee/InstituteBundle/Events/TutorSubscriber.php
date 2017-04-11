<?php

namespace Busybee\InstituteBundle\Events ;

use Busybee\InstituteBundle\Repository\SpaceRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TutorSubscriber implements EventSubscriberInterface
{
    /**
     * @var SpaceRepository
     */
    private $sr;

    /**
     * TutorSubscriber constructor.
     * @param SpaceRepository $sr
     */
    public function __construct(SpaceRepository $sr)
    {
        $this->sr = $sr;
    }

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

        if (empty($data['tutor2']))
        {
            if (! empty($data['tutor3']))
            {
                $data['tutor2'] = $data['tutor3'];
                $data['tutor3'] = '';
            }
        }
        if (empty($data['tutor1']))
        {
            if (! empty($data['tutor2']))
            {
                $data['tutor1'] = $data['tutor2'];
                $data['tutor2'] = '';          }
        }
        if (empty($data['tutor1']) && !empty($data['space']))
        {
            $cr = $this->sr->find($data['space']);
            if (! empty($cr->getStaff1()) && $cr->getStaff1()->getId() > 0)
                $data['tutor1'] = $cr->getStaff1()->getId();
        }

        if (! empty($data['tutor1']) && $data['tutor1'] == $data['tutor2']) $data['tutor2'] = '';
        if (! empty($data['tutor1']) && $data['tutor1'] == $data['tutor3']) $data['tutor3'] = '';
        if (! empty($data['tutor2']) && $data['tutor2'] == $data['tutor3']) $data['tutor3'] = '';

        $event->setData($data);
    }
}