<?php
namespace Busybee\Management\GradeBundle\Events;

use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\Management\GradeBundle\Entity\StudentGrade;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class StudentSubscriber implements EventSubscriber
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

		if ($metadata->getName() == Student::class)
		{
			$metadata->mapOneToMany(
				[
					'targetEntity' => StudentGrade::class,
					'fieldName'    => 'grades',
					'cascade'      => ['persist'],
					'mappedBy'     => 'student',
				]
			);
		}

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