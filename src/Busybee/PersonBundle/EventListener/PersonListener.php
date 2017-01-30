<?php
namespace Busybee\PersonBundle\EventListener ;

use Busybee\SecurityBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Mapping\ContainerAwareEntityListenerResolver;
use Doctrine\ORM\Event\LifecycleEventArgs ;
use Doctrine\ORM\Event\PreUpdateEventArgs ;
use Busybee\PersonBundle\Entity\Person ;
use Busybee\PersonBundle\Model\PhotoUploader ;
use Symfony\Component\HttpFoundation\File\File ;

class PersonListener
{
    private $uploader;

    private $em ;
    /**
     * PersonListener constructor.
     * @param PhotoUploader $uploader
     */
    public function __construct(PhotoUploader $uploader)
    {
        $this->uploader = $uploader;
        $this->em = null ;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User)
        {
            $entity->setUsernameCanonical($entity->getUsername());
            $entity->setEmailCanonical($entity->getEmail());
        } elseif ($entity instanceof Person)
        {
            $this->em = $args->getEntityManager();
            $entity = $this->setIdentifierValue($entity);
        }
    }

    /**
     * @param $entity
     * @return mixed
     */
    private function setIdentifierValue($entity)
    {
        $identifier = $this->createIdentifierValue($entity);

        $x = $this->createIdentityKey($identifier, $entity->getId());

        $identifier .= str_pad(strval($x), 2, '0', STR_PAD_LEFT);

        $entity->setIdentifier(strtoupper($identifier));

        return $entity;
    }

    /**
     * @param $entity
     * @return string
     */
    private function createIdentifierValue($entity)
    {
        $identifier = '';
        $identifier .= mb_substr($entity->getSurname(), 0, 2);
        $name = trim(str_replace($entity->getSurname(), '', $entity->getOfficialName()));
        $name = explode(' ', $name);
        if ($name[0])
            $identifier .= mb_substr($name[0], 0, 1);
        if ($name[1])
            $identifier .= mb_substr($name[1], 0, 1);
        $identifier = str_pad($identifier, 4, '*');
        if ($entity->getDob() instanceof \DateTime)
            $identifier .= $entity->getDob()->format('dm');
        $identifier = str_pad($identifier, 8, '*');
        return $identifier;
    }

    /**
     * @param $identifier
     * @return int
     */
    private function createIdentityKey($identifier, $id)
    {
        $x = 0;
        $notValid = true ;

        while($notValid)
        {
            $test = $identifier . str_pad(strval($x), 2, '0', STR_PAD_LEFT);
            if ($this->em->getRepository(Person::class)->createQueryBuilder('p')
                    ->select('COUNT(p.id)')
                    ->where('p.identifier = :identifier')
                    ->andWhere('p.id != :id')
                    ->setParameter('identifier', $test)
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->getSingleScalarResult() > 0)
                $x++;
            else
                $notValid = false;
        }

        return $x;
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Person)
        {
            $this->em = $args->getEntityManager();
            $entity = $this->modifyIdentifierValue($entity);
        }
    }

    /**
     * @param $entity
     * @return mixed
     */
    private function modifyIdentifierValue($entity)
    {
        $identifier = $this->createIdentifierValue($entity);

        $x = $this->createIdentityKey($identifier, $entity->getId());

        $identifier .= str_pad(strval($x), 2, '0', STR_PAD_LEFT);

        $entity->setIdentifier(strtoupper($identifier));

        return $entity;
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