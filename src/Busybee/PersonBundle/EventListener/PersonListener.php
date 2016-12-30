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

    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

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

}