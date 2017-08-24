<?php

namespace Busybee\AVETMISS\AVETMISSBundle\EventListener;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Busybee\AVETMISS\AVETMISSBundle\Entity\Course;
use Busybee\AVETMISS\AVETMISSBundle\Entity\Subject;
use Busybee\CurriculumBundle\Entity\Course as CourseCore;
use Busybee\CurriculumBundle\Entity\Subject as SubjectCore;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Busybee\PersonBundle\Entity\Person;
use Doctrine\ORM\Event\PreFlushEventArgs;


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