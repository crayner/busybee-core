<?php
namespace Busybee\PersonBundle\EventListener ;

use Symfony\Component\HttpFoundation\File\UploadedFile ;
use Doctrine\ORM\Event\LifecycleEventArgs ;
use Doctrine\ORM\Event\PreUpdateEventArgs ;
use Busybee\PersonBundle\Entity\Person ;
use Busybee\PersonBundle\Entity\Phone ;
use Busybee\PersonBundle\Model\PhotoUploader ;
use Symfony\Component\HttpFoundation\File\File ;

class PersonListener
{
    private $uploader;

    public function __construct(PhotoUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

		$this->checkPreferredName($entity);
		$this->checkAddresses($entity);

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

		$this->checkPreferredName($entity);
		$this->checkAddresses($entity);

        $this->uploadFile($entity);
    }

    private function uploadFile($entity)
    {
        // upload only works for Person entities
        if ($entity instanceof Person)
        {
			$fileName = $this->uploader->upload($entity);
			$entity->setPhoto($fileName);
		}
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Person)
		{
			$file = file_exists($entity->getPhoto()) ? $entity->getPhoto() : null ;
			$file = is_null($file) ? new File(null, false) : new File($file, true);
			$entity->setPhoto($file);
		}
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Person)
		{
			$entity->removePhotoFile();
		}
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Person)
		{
			$entity->removePhotoFile();
		}
    }

    private function checkPreferredName($entity)
    {
        // preferred name in Person Entity
        if ($entity instanceof Person)
        {
			if (empty($entity->getPreferredName()))
				$entity->setPreferredName($entity->getFirstName());
		}
    }

    private function checkAddresses($entity)
    {
        // preferred name in Person Entity
        if ($entity instanceof Person)
        {
			$address1 = $entity->getAddress1();
			$address2 = $entity->getAddress2();
			
			if (is_null($address1) && is_null($address2)) return ;
			
			if (is_null($address1) && ! is_null($address2))
			{
				$entity->setAddress1($address2);
				$entity->setAddress2(null);
				return ;
			}
			if (is_null($address2)) return ;
dump($address1);
dump($address2);			
			if ($address1->getId() == $address2->getId())
			{
				$entity->setAddress2(null);
				return ;
			}
		}
    }
}