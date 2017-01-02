<?php

namespace Busybee\FamilyBundle\Events ;

use Busybee\FamilyBundle\Model\FamilyManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FamilySubscriber implements EventSubscriberInterface
{
    /**
     * @var FamilyManager
     */
    private $fm;

    /**
     * FamilySubscriber constructor.
     * @param FamilyManager $pm
     */
    public function __construct(FamilyManager $fm)
    {
        $this->fm = $fm;
    }

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

        unset($data['address1_list'], $data['address2_list']);

        //Check Care Give Data
        if (empty($data['careGiver1']) && ! empty($data['careGiver2']))
        {
            $data['careGiver1'] = $data['careGiver2'];
            $data['careGiver2'] = "";
        }
        if (! empty($data['careGiver1']) && $data['careGiver2'] == $data['careGiver1'])
            $data['careGiver2'] = "";

        $ec = array();
        if (! empty($data['careGiver1'])) $ec[] = $data['careGiver1'];
        if (! empty($data['careGiver2'])) $ec[] = $data['careGiver2'];

        if (empty($data['name']))
            $data['name'] = $this->fm->generateFamilyName($data);

        if (is_array($data['emergencyContact']))
            foreach($data['emergencyContact'] as $key=>$id)
                if (in_array($id, $ec))
                {
                    unset($data['emergencyContact'][$key]);
                } else {
                    $ec[] = $id;
                }

        $students = array();
        if (is_array($data['student']))
            foreach($data['student'] as $key=>$id)
                if (in_array($id, $students))
                {
                    unset($data['student'][$key]);
                } else {
                    $students[] = $id;
                }


        $event->setData($data);
    }
}