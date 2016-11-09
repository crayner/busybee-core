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

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

		$this->checkPreferredName($entity);

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
			$file = is_null($entity->getPhoto()) ? null : new File($entity->getPhoto(), true);
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
}