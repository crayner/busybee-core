<?php

namespace Busybee\AVETMISS\AVETMISSBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;


class AVETMISSListener
{
	private $request;

	public function __construct(RequestStack $request)
	{
		$r = $request->getCurrentRequest();
		if ($r instanceof Request)
			$this->request = $r->request;
		else
			$this->request = null;
	}

	public function prePersist(LifecycleEventArgs $args)
	{

		$entity = $args->getEntity();
	}

	public function preUpdate(PreUpdateEventArgs $args)
	{
		$entity = $args->getEntity();
	}

}