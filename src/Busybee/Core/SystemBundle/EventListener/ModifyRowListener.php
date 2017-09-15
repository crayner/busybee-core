<?php

namespace Busybee\Core\SystemBundle\EventListener;

use Busybee\Core\SystemBundle\Entity\Setting;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Busybee\Core\SecurityBundle\Entity\User;

class ModifyRowListener implements EventSubscriberInterface
{

	private $container;
	private $user;

	public function __construct(Container $container)
	{
		$this->container = $container;
		$this->user      = null;
	}

	public static function getSubscribedEvents()
	{
		return array(
			'prePersist',
			'preUpdate'
		);
	}


	public function prePersist(LifecycleEventArgs $args)
	{
		$entity        = $args->getEntity();
		$entityManager = $args->getEntityManager();

		if ($entity instanceof User && intval($this->getCurrentUser()->getId()) == 0)
		{
			$entity->setCreatedOn(new \Datetime('now'));
			$entity->setCreatedBy(null);
			$entity->setLastModified(new \Datetime('now'));
			$entity->setModifiedBy(null);
		}
		else
		{
			$entity->setCreatedOn(new \Datetime('now'));
			$entity->setCreatedBy($this->getCurrentUser());
			$entity->setLastModified(new \Datetime('now'));
			$entity->setModifiedBy($this->getCurrentUser());
		}
		if (!is_null($entity->getCreatedBy()) && $entityManager->getUnitOfWork()->isScheduledForInsert($entity->getCreatedBy()))
		{
			$entityManager->detach($entity->getCreatedBy());
		}
		if (!is_null($entity->getModifiedBy()) && $entityManager->getUnitOfWork()->isScheduledForInsert($entity->getModifiedBy()))
		{
			$entityManager->detach($entity->getModifiedBy());
		}
	}

	private function getCurrentUser()
	{
		if (!is_null($this->user)) return $this->user;
		$token = $this->container->get('security.token_storage')->getToken();
		if (!is_null($token)) $this->user = $token->getUser();
		if (is_null($this->user))
		{
			$session = $this->container->get('session');
			$token   = unserialize($session->get('_security_default'));
			if ($token instanceof TokenInterface)
				$this->user = $token->getUser();
		}
		if (!$this->user instanceof User)
			$this->user = $this->container->get('busybee_core_security.repository.user_repository')->find(1);

		return $this->user;
	}

	/**
	 * @param PreUpdateEventArgs $args
	 *
	 * @throws \Exception
	 */
	public function preUpdate(PreUpdateEventArgs $args)
	{
		$entity        = $args->getEntity();
		$entityManager = $args->getEntityManager();;
		$entity->setLastModified(new \Datetime('now'));
		$entity->setModifiedBy($this->getCurrentUser());

		$x = (array) $entity;

		if (!is_null($entity->getModifiedBy()) && $entityManager->getUnitOfWork()->isScheduledForInsert($entity->getModifiedBy()))
		{
			$entityManager->detach($entity->getModifiedBy());
		}

		if ($entity instanceof Setting)
		{
			if ($entity->getSecurityActive())
				if (true !== $this->get('busybee_core_security.model.authorisation')->redirectAuthorisation($entity->getRole()->getRole()))
				{
					throw new \Exception ('Settings cannot be updated without a user');
				}
		}
	}
}
