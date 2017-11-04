<?php

namespace Busybee\Facility\InstituteBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;

class DepartmentManager
{
	/**
	 * @var ObjectManager
	 */
	private $objectManager;

	/**
	 * DepartmentManager constructor.
	 *
	 * @param ObjectManager $objectManager
	 */
	public function __construct(ObjectManager $objectManager)
	{
		$this->objectManager = $objectManager;
	}

	/**
	 * Course Installed
	 *
	 * @return bool
	 */
	public function courseInstalled(): bool
	{
		if (class_exists('Busybee\Program\CurriculumBundle\Model\CourseManager'))
		{
			$metaData = $this->getObjectManager()->getClassMetadata('Busybee\Program\CurriculumBundle\Entity\Course');
			$schema   = $this->getObjectManager()->getConnection()->getSchemaManager();

			return $schema->tablesExist([$metaData->table['name']]);

		}

		return false;
	}

	/**
	 * Get ObjectManager
	 *
	 * @return ObjectManager
	 */
	public function getObjectManager()
	{
		return $this->objectManager;
	}
}