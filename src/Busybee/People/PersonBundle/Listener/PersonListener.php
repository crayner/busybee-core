<?php

namespace Busybee\People\PersonBundle\Listener;


use Busybee\People\PersonBundle\Entity\Person;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class PersonListener
{
	/**
	 * @var ObjectManager
	 */
	private $em;

	/**
	 * @param LifecycleEventArgs $args
	 */
	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();
		if ($entity instanceof Person)
		{
			$this->em = $args->getEntityManager();
			$entity   = $this->setIdentifierValue($entity);
		}
	}

	/**
	 * @param $entity
	 *
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
	 *
	 * @return string
	 */
	private function createIdentifierValue($entity)
	{
		$identifier = '';
		$identifier .= mb_substr($entity->getSurname(), 0, 2);
		$name       = trim(str_replace($entity->getSurname(), '', $entity->getOfficialName()));
		$name       = explode(' ', $name);
		if (!empty($name[0]))
			$identifier .= mb_substr($name[0], 0, 1);
		if (!empty($name[1]))
			$identifier .= mb_substr($name[1], 0, 1);
		$identifier = str_pad($identifier, 4, '*');
		if ($entity->getDob() instanceof \DateTime)
			$identifier .= $entity->getDob()->format('dm');
		$identifier = str_pad($identifier, 8, '*');

		return $identifier;
	}

	/**
	 * @param $identifier
	 *
	 * @return int
	 */
	private function createIdentityKey($identifier, $id)
	{
		$x        = 0;
		$notValid = true;

		while ($notValid)
		{
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

	/**
	 * @param PreUpdateEventArgs $args
	 */
	public function preUpdate(PreUpdateEventArgs $args)
	{
		$entity = $args->getEntity();
		if ($entity instanceof Person)
		{
			$this->em = $args->getEntityManager();
			$entity   = $this->setIdentifierValue($entity);
		}
	}
}