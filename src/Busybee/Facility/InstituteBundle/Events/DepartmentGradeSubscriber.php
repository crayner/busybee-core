<?php

namespace Busybee\Facility\InstituteBundle\Events;

use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\Facility\InstituteBundle\Entity\Space;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class DepartmentGradeSubscriber implements EventSubscriber
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
			$metadata->mapOneToOne(
				[
					'targetEntity' => Space::class,
					'fieldName'    => 'space',
					'mappedBy'     => 'grade',
					'joinColumn'   => [
						'name'                 => 'space_id',
						'referencedColumnName' => 'id',
						'nullable'             => true,
					],

				]
			);
		}
		if ($metadata->getName() == Space::class)
		{
			$metadata->mapOneToOne(
				[
					'targetEntity' => Grade::class,
					'fieldName'    => 'grade',
					'inversedBy'   => 'space',
					'joinColumn'   => [
						'name'                 => 'grade_id',
						'referencedColumnName' => 'id',
						'nullable'             => true,
					],

				]
			);
		}
	}
}