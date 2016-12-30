<?php

namespace Busybee\PersonBundle\Events ;

use Busybee\InstituteBundle\Repository\CampusResourceRepository;
use Busybee\PersonBundle\Entity\Phone;
use Busybee\PersonBundle\Model\PhotoUploader;
use Busybee\PersonBundle\Repository\PhoneRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonSubscriber implements EventSubscriberInterface
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
        $form = $event->getForm();

        $data = $this->checkPreferredName($data);
        $data = $this->checkAddresses($data);
        $data = $this->uploadFile($data);

        $event->setData($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    private function checkPreferredName($data)
    {
        if (empty($data['preferredName']))
            $data['preferredName'] = $data['firstName'];
        return $data ;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function checkAddresses($data)
    {
        $address1 = $data['address1'];
        $address2 = $data['address2'];

        if (empty($address1) && empty($address2)) return $data;

        if (empty($address1) && ! empty($address2))
        {
            $data['address1'] = $data['address2'];
            $data['address2'] = '';
            return $data;
        }

        if (empty($address2)) return $data;

        if ($address1 == $address2)
            $data['address2'] = '';

        return $data ;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function uploadFile($data)
    {
        $data['photo'] = $this->uploader->upload($data['photo']);
        return $data;
    }
    private $uploader;

    /**
     * PersonSubscriber constructor.
     * @param PhotoUploader $photoUpLoader
     */
    public function __construct(PhotoUploader $photoUpLoader)
    {
        $this->uploader = $photoUpLoader ;
    }
}