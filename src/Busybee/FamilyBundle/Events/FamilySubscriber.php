<?php

namespace Busybee\FamilyBundle\Events;

use Busybee\FamilyBundle\Model\FamilyManager;
use Doctrine\Common\Collections\ArrayCollection;
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

        $form = $event->getForm();

        unset($data['address1_list'], $data['address2_list']);

        $careGiver = array();
        if (!empty($data['careGiver']) && is_array($data['careGiver'])) {
            foreach ($data['careGiver'] as $q => $w)
                if (!empty($w) && !empty($w['person'])) {
                    $w['contactPriority'] = $q + 1;
                    $careGiver[] = $w;
                }
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

        $family = $form->getData();
        $careGivers = new ArrayCollection();

        if (is_array($data['careGiver'])) {
            foreach ($data['careGiver'] as $q => $w) {
                $data['careGiver'][$q]['contactPriority'] = $q + 1;
            }

            if ($family->getId() > 0) {
                foreach ($data['careGiver'] as $q => $w) {
                    $cg = $this->fm->findOneCareGiverByPerson(array('person' => $w['person'], 'family' => $family->getId()));
                    $data['careGiver'][$q]['cg'] = $cg;
                    $data['careGiver'][$q]['family'] = $family->getId();
                }

                usort($data['careGiver'], function ($item1, $item2) {
                    return $item1['currentOrder'] <=> $item2['currentOrder'];
                });
            }

            foreach ($data['careGiver'] as $q => $w) {
                if (!empty($data['careGiver'][$q]['cg']))
                    $careGivers->add($data['careGiver'][$q]['cg']);
                unset($data['careGiver'][$q]['cg'], $data['careGiver'][$q]['currentOrder']);
            }
        }

        $a = $family->getCareGiver()->toArray();
        $b = $careGivers->toArray();
        $xx = array_diff($a, $b);
        foreach ($xx as $cg)
            $this->fm->removeEntity($cg);
        $family->setCareGiver($careGivers);

        $students = new ArrayCollection();
        if (!empty($data['students']) && is_array($data['students'])) {
            foreach ($data['students'] as $q => $w)
                if (!empty($w) && !empty($w['person'])) {
                    $student = $this->fm->getStudentFromPerson($w['person']);
                    $students->add($student);
                }
        }


        $family->setStudents($students);

        $form->setData($family);

        $event->setData($data);
    }

}