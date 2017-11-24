<?php

namespace Busybee\People\StudentBundle\Events;

use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\People\StudentBundle\Entity\StudentGrade;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class StudentGradeSubscriber implements EventSubscriber
{
	/**
	 * {@inheritDoc}
	 */
	public function getSubscribedEvents()
	{
		return [
			Events::loadClassMetadata,
		];
	}

	/**
	 * @param LoadClassMetadataEventArgs $eventArgs
	 */
	public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
	{
		// the $metadata is the whole mapping info for this class
		$metadata = $eventArgs->getClassMetadata();

		if ($metadata->getName() == Grade::class)
		{
			$metadata->mapOneToMany(
				[
					'targetEntity' => StudentGrade::class,
					'fieldName'    => 'students',
					'cascade'      => ['persist'],
					'mappedBy'     => 'grade',
				]
			);
		}
	}
}