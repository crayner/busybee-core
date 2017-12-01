<?php
namespace Busybee\People\StudentBundle\Events;

use Busybee\Core\CalendarBundle\Entity\CalendarGroup;
use Busybee\People\StudentBundle\Entity\StudentCalendarGroup;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class StudentCalendarGroupSubscriber implements EventSubscriber
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
			$metadata->mapOneToMany(
				[
					'targetEntity' => StudentCalendarGroup::class,
					'fieldName'    => 'students',
					'cascade'      => ['persist'],
					'mappedBy'     => 'calendarGroup',
				]
			);
		}
	}
}