<?php

namespace Busybee\Facility\InstituteBundle\Events;

use Busybee\Facility\InstituteBundle\Entity\DepartmentStaff;
use Busybee\Facility\InstituteBundle\Entity\Space;
use Busybee\People\StaffBundle\Entity\Staff;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class DepartmentStaffSubscriber implements EventSubscriber
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

		if ($metadata->getName() == Staff::class)
		{
			$metadata->mapOneToMany(
				[
					'targetEntity'  => Space::class,
					'fieldName'     => 'spaces',
					'cascade'       => ['persist', 'remove'],
					'mappedBy'      => 'staff',
					'orphanRemoval' => true,
					'orderBy'       => ['name' => 'ASC'],
				]
			);
		}
	}

}