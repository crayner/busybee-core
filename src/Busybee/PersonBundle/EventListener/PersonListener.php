<?php
namespace Busybee\PersonBundle\EventListener ;

use Busybee\FamilyBundle\Entity\Family;
use Busybee\StudentBundle\Entity\Student;
use Busybee\SecurityBundle\Entity\User;
use Doctrine\ORM\Event\LifecycleEventArgs ;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs ;
use Busybee\PersonBundle\Entity\Person ;
use Busybee\PersonBundle\Model\PhotoUploader ;

class PersonListener
{
    private $em ;

    /**
     * PersonListener constructor.
     * @param PhotoUploader $uploader
     */
    public function __construct()
    {
        $this->em = null ;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            $entity->setUsernameCanonical($entity->getUsername());
            $entity->setEmailCanonical($entity->getEmail());
        }
        if ($entity instanceof Person) {
            $this->em = $args->getEntityManager();
            $entity = $this->setIdentifierValue($entity);
        }
        if ($entity instanceof Student) {
            if (empty($entity->getStatus()))
                $entity->setStatus('Future');
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
        if (! empty($name[0]))
            $identifier .= mb_substr($name[0], 0, 1);
        if (! empty($name[1]))
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

        while($notValid) {
            $test = strtoupper($identifier . str_pad(strval($x), 2, '0', STR_PAD_LEFT));
            if ($this->em->getRepository(Person::class)->createQueryBuilder('p')
                    ->select('COUNT(p.id)')
                    ->where('p.identifier = :identifier')
                    ->andWhere('p.id != :id')
                    ->setParameter('identifier', $test)
                    ->setParameter('id', intval($id))
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
        if ($entity instanceof Person) {
            $this->em = $args->getEntityManager();
            $entity = $this->setIdentifierValue($entity);
        }
        if ($entity instanceof Student) {
            if (empty($entity->getStatus()))
                $entity->setStatus('Future');
        }
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
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
}