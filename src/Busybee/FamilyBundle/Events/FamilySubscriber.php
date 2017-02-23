<?php

namespace Busybee\FamilyBundle\Events;

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

        if (!empty($data['careGiver']) && is_array($data['careGiver'])) {
            $careGiver = array();
            foreach ($data['careGiver'] as $q => $w)
                if (!empty($w) && !empty($w['person']))
                    $careGiver[] = $w;
        }

        $data['careGiver'] = $careGiver;

        if (empty($data['careGiver']) || empty($data['careGiver'][0]['person']))
            unset($data['careGiver']);

        if (empty($data['name']))
            $data['name'] = $this->fm->generateFamilyName($data);

        $students = array();
        if (isset($data['students']) && is_array($data['students']))
            foreach ($data['students'] as $key => $id)
                if (in_array($id, $students)) {
                    unset($data['students'][$key]);
                } else {
                    $students[] = $id;
                }

        // Address Management
        unset($data['address1_list'], $data['address2_list']);
        if (!empty($data['address1']) || !empty($data['address2'])) {
            if ($data['address1'] == $data['address2'])
                $data['address2'] = "";
            elseif (empty($data['address1']) && !empty($data['address2'])) {
                $data['address1'] = $data['address2'];
                $data['address2'] = "";
            }
        }

        $event->setData($data);
    }
}