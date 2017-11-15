<?php
namespace Busybee\Facility\InstituteBundle\Events;

use Busybee\Facility\InstituteBundle\Entity\DepartmentMember;
use Busybee\Facility\InstituteBundle\Entity\Space;
use Busybee\People\StaffBundle\Entity\Staff;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DepartmentMemberSubscriber implements EventSubscriber
{
	/**
	 * @return array
	 */
	public function getSubscribedEvents()
	{
		// Tells the dispatcher that you want to listen on the form.pre_submit
		// event and that the preSubmit method should be called.
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

		if ($metadata->getName() == Staff::class)
		{
			$metadata->mapOneToOne(
				[
					'targetEntity' => Space::class,
					'fieldName'    => 'homeroom',
					'cascade'      => ['persist'],
					'mappedBy'     => 'staff',
					'orderBy'      => ['name' => 'ASC'],
					'joinColumn'   => [
						'name'                 => 'space_id',
						'referencedColumnName' => 'id',
					],
				]
			);
			$metadata->mapOneToMany(
				[
					'targetEntity'  => DepartmentMember::class,
					'fieldName'     => 'departments',
					'cascade'       => ['all'],
					'mappedBy'      => 'member',
					'orphanRemoval' => true,
				]
			);

		}
	}
}