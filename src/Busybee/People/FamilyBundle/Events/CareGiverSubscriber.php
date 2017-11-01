<?php

namespace Busybee\People\FamilyBundle\Events;

use Busybee\People\FamilyBundle\Entity\CareGiver;
use Busybee\People\PersonBundle\Entity\Person;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class CareGiverSubscriber implements EventSubscriber
{
	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_submit
		// event and that the preSubmit method should be called.
		return array(
			Events::loadClassMetadata,

		);
	}

	/**
	 * @param LoadClassMetadataEventArgs $eventArgs
	 */
	public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
	{
		// the $metadata is the whole mapping info for this class
		$metadata = $eventArgs->getClassMetadata();

		if ($metadata->getName() == Person::class)
		{
			$metadata->mapOneToMany(
				[
					'targetEntity'  => CareGiver::class,
					'fieldName'     => 'careGivers',
					'cascade'       => ['persist', 'remove'],
					'mappedBy'      => 'person',
					'orphanRemoval' => true,
					'orderBy'       => ['contactPriority' => 'ASC'],
				]
			);
		}
	}

}