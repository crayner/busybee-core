<?php

namespace Busybee\People\StaffBundle\Events;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Busybee\People\StaffBundle\Entity\Staff;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class StaffCalendarGroupSubscriber implements EventSubscriber
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

		if ($metadata->getName() == CalendarGroup::class)
		{
			$metadata->mapManyToOne(
				[
					'targetEntity' => Staff::class,
					'fieldName'    => 'yearTutor',
					'inversedBy'   => 'calendarGroups',
					'cascade'      => ['persist'],
					'joinColumn'   => [
						'name'                 => 'year_tutor_id',
						'referencedColumnName' => 'id',
					],
				]
			);
		}
	}
}