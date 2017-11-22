<?php
namespace Busybee\Program\GradeBundle\Events;

use Busybee\Core\CalendarBundle\Entity\Grade;
use Busybee\People\StaffBundle\Entity\Staff;
use Busybee\Program\GradeBundle\Entity\StudentGrade;
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
			$metadata->mapOneToOne(
				[
					'targetEntity' => Staff::class,
					'fieldName'    => 'tutor1',
					'joinColumn'   => [
						'name'                 => 'tutor_1',
						'referencedColumnName' => 'id',
						'nullable'             => true,
					],

				]
			);
			$metadata->mapOneToOne(
				[
					'targetEntity' => Staff::class,
					'fieldName'    => 'tutor2',
					'joinColumn'   => [
						'name'                 => 'tutor_2',
						'referencedColumnName' => 'id',
						'nullable'             => true,
					],

				]
			);
			$metadata->mapOneToOne(
				[
					'targetEntity' => Staff::class,
					'fieldName'    => 'tutor3',
					'joinColumn'   => [
						'name'                 => 'tutor_3',
						'referencedColumnName' => 'id',
						'nullable'             => true,
					],

				]
			);
		}
	}
}