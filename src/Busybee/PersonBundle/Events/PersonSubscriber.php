<?php

namespace Busybee\PersonBundle\Events ;

use Busybee\PersonBundle\Entity\CareGiver;
use Busybee\PersonBundle\Entity\Staff;
use Busybee\PersonBundle\Entity\Student;
use Busybee\PersonBundle\Form\CareGiverType;
use Busybee\PersonBundle\Form\StaffType;
use Busybee\PersonBundle\Form\StudentType;
use Busybee\PersonBundle\Form\UserType;
use Busybee\PersonBundle\Model\PersonManager;
use Busybee\SecurityBundle\Entity\User;
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
     * @var array
     */
    private $parameters ;

    /**
     * PersonSubscriber constructor.
     * @param PersonManager $pm
     * @param ObjectManager $om
     * @param $parameters
     */
    public function __construct(PersonManager $pm, ObjectManager $om, $parameters)
    {
        $this->personManager = $pm;
        $this->om = $om ;
        $this->parameters = $parameters;
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

        if ($person->getCareGiver() === null || $person->getCareGiver()->getId() === null)
            $form->add('careGiver', HiddenType::class);
        elseif ($this->personManager->canBeStaff($person))
            $form->add('careGiver', CareGiverType::class);
        else
            $form->add('careGiver', HiddenType::class);

        if ($person->getStudent() === null || $person->getStudent()->getId() === null)
            $form->add('student', HiddenType::class);
        elseif ($this->personManager->canBeStudent($person))
            $form->add('student', StudentType::class);
        else
            $form->add('student', HiddenType::class);

        if ($person->getUser() === null || $person->getUser()->getId() === null)
            $form->add('user', HiddenType::class);
        elseif ($this->personManager->canBeUser($person)) {
            $form->add('user', UserType::class);
            if (empty($person->getUser()->getEmail()) || $person->getUser()->getEmail() != $person->getEmail())
                $person->getUser()->setEmail($person->getEmail());
        }
        else
            $form->add('user', HiddenType::class);

        $event->setData($person);
    }
    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $person = $form->getData();
        $flush = false ;

        if (isset($data['staffQuestion']) && $data['staffQuestion'] === '1' && ! $person->getStaff() instanceof Staff && $this->personManager->canBeStaff($person))
        {
            $data['staff'] = array();
            $data['staff']['type'] = 'Unknown';
            $data['staff']['jobTitle'] = 'Not Specified';
            $data['staff']['person'] = $form->getData()->getId();
            $form->remove('staff');
            $form->add('staff', StaffType::class);
        }

        if ($form->get('staff')->getData() instanceof Staff  && ! isset($data['staff']) && $this->personManager->canDeleteStaff($person))
        {
            $data['staff'] = "";
            $form->remove('staff');
            $form->add('staff', HiddenType::class);
            $this->om->remove($form->get('staff')->getData());
            $flush = true ;
        }

        if (isset($data['careGiverQuestion']) && $data['careGiverQuestion'] === '1' && ! $person->getCareGiver() instanceof CareGiver && $this->personManager->canBeCareGiver($person))
        {
            $data['careGiver'] = array();
            $data['careGiver']['person'] = $form->getData()->getId();
            $form->remove('careGiver');
            $form->add('careGiver', CareGiverType::class);
        }

        if ($form->get('careGiver')->getData() instanceof CareGiver && ! isset($data['careGiver']) && $this->personManager->canDeleteCareGiver($person))
        {
            $data['careGiver'] = "";
            $form->remove('careGiver');
            $form->add('careGiver', HiddenType::class);
            $this->om->remove($form->get('careGiver')->getData());
            $flush = true;
        }

        if ( isset($data['studentQuestion']) && $data['studentQuestion'] === '1' && ! $person->getStudent() instanceof Student && $this->personManager->canBeStudent($person))
        {
            $data['student'] = array();
            $data['student']['startAtSchool'] = array();
            $data['student']['startAtSchool']['year'] = date('Y');
            $data['student']['startAtSchool']['month'] = date('n');
            $data['student']['startAtSchool']['day'] = date('j');
            $data['student']['startAtThisSchool'] = array();
            $data['student']['startAtThisSchool']['year'] = date('Y');
            $data['student']['startAtThisSchool']['month'] = date('n');
            $data['student']['startAtThisSchool']['day'] = date('j');
            $data['student']['person'] = $form->getData()->getId();
            $form->remove('student');
            $form->add('student', StudentType::class);
        }

        if ($form->get('student')->getData() instanceof Student  && ! isset($data['studentQuestion']) && $this->personManager->canDeleteStudent($person, $this->parameters))
        {
            $data['student'] = "";
            $form->remove('student');
            $form->add('student', HiddenType::class);
            $this->om->remove($form->get('student')->getData());
            $flush = true ;
        }

        if (isset($data['userQuestion']) && $data['userQuestion'] === '1' && ! $person->getUser() instanceof User && $this->personManager->canBeUser($person))
        {
            $data['user'] = array();
            $data['user']['person'] = $form->getData()->getId();
            $data['user']['email'] = $data['user']['emailCanonical'] = $data['email'];
            $data['user']['username'] = $data['user']['usernameCanonical'] = $data['email'];
            $data['user']['locale'] = $this->personManager->getParameter('locale');
            $data['user']['enabled'] = true;
            $data['user']['locked'] = false;
            $data['user']['expired'] = false;
            $data['user']['credentials_expired'] = true;
            $data['user']['password'] = password_hash(uniqid(), PASSWORD_BCRYPT);
            $user = $this->personManager->doesThisUserExist($person);
            if (is_null($user))
            {
                $form->remove('user');
                $form->add('user', UserType::class);
            }
            else
            {

            }

        }

        if ($form->get('user')->getData() instanceof User && isset($data['userQuestion']))
        {
            $data['user']['usernameCanonical'] = $data['user']['username'];
            $data['user']['email'] = $data['user']['emailCanonical'] = $data['email'];
        }

        if ($form->get('user')->getData() instanceof User && ! isset($data['user']) && $this->personManager->canDeleteUser($person))
        {
            $data['user'] = "";
            $form->remove('user');
            $form->add('user', HiddenType::class);
            $this->om->remove($form->get('user')->getData());
            $flush = true;
        }

        // Address Management
        unset($data['address1_list'], $data['address2_list']);
        if (! empty($data['address1']) || ! empty($data['address2']))
        {
            if ($data['address1'] == $data['address2'])
                $data['address2'] = "";
            elseif (empty($data['address1']) && ! empty($data['address2']))
            {
                $data['address1'] = $data['address2'];
                $data['address2'] = "";
            }
        }

        // Email Management
        if (! empty($data['email']) || ! empty($data['email2']))
        {
            if ($data['email'] == $data['email2'])
                $data['email2'] = "";
            elseif (empty($data['email']) && ! empty($data['email2']))
            {
                $data['email'] = $data['email2'];
                $data['email2'] = "";
            }
        }


        if ($flush)
            $this->om->flush();

        if (empty($data['preferredName']))
            $data['preferredName'] = $data['firstName'];

        $event->setData($data);
    }
}