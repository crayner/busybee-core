<?php

namespace Busybee\PersonBundle\Events ;

use Busybee\PersonBundle\Entity\Staff;
use Busybee\PersonBundle\Form\StaffType;
use Busybee\PersonBundle\Model\PersonManager;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonSubscriber implements EventSubscriberInterface
{
    /**
     * @var PersonManager
     */
    private $personManager ;

    /**
     * @var ObjectManager
     */
    private $om ;

    /**
     * PersonSubscriber constructor.
     * @param PersonManager $pm
     * @param ObjectManager $om
     */
    public function __construct(PersonManager $pm, ObjectManager $om)
    {
        $this->personManager = $pm;
        $this->om = $om ;
    }
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_submit
        // event and that the preSubmit method should be called.
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $person = $event->getData();
        $form = $event->getForm();

        if ($person->getStaff() === null || $person->getStaff()->getId() === null)
            $form->add('staff', HiddenType::class);
        elseif ($this->personManager->canBeStaff($person))
            $form->add('staff', StaffType::class);
        else
            $form->add('staff', HiddenType::class);
    }
    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $person = $form->getData();

        if (isset($data['staff']) && $data['staff'] === '' && isset($data['staffQuestion']) && $data['staffQuestion'] === '1')
        {
            $data['staff'] = array();
            $data['staff']['type'] = 'Unknown';
            $data['staff']['jobTitle'] = 'Not Specified';
            $data['staff']['person'] = $form->getData();
            $form->remove('staff');
            $form->add('staff', StaffType::class);
        }

        if ($form->get('staff')->getData() instanceof Staff  && ! isset($data['staff']) && $this->personManager->canDeleteStaff($person))
        {
            $data['staff'] = "";
            $form->remove('staff');
            $form->add('staff', HiddenType::class);
            $this->om->remove($form->get('staff')->getData());
            $this->om->flush();
        }

        $event->setData($data);
    }
}