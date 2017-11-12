<?php

namespace GibbonBundle\Model;

use Busybee\Core\HomeBundle\Exception\Exception;
use Busybee\Facility\InstituteBundle\Entity\Department;
use Busybee\People\FamilyBundle\Entity\CareGiver;
use Busybee\People\FamilyBundle\Entity\Family;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\People\StudentBundle\Entity\Student;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class ImportDepartments extends ImportManager
{
	/**
	 * @var int
	 */
	private $count;

	/**
	 * @var int
	 */
	private $offset;

	/**
	 * @var array
	 */
	private $departmentTypes;

	/**
	 * ImportPeople constructor.
	 *
	 * @param ObjectManager $gibbonManager
	 * @param ObjectManager $manager
	 * @param PersonManager $personManager
	 */
	public function __construct(ObjectManager $gibbonManager, ObjectManager $manager, PersonManager $personManager)
	{
		parent::__construct($gibbonManager, $manager, $personManager);

		$sql = "SELECT COUNT(`gibbonDepartmentID`) as `CC` FROM `gibbonDepartment`";

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$this->count = $stmt->fetch();
		$this->count = $this->count['CC'];

		$this->departmentTypes = $this->getPersonManager()->getSm()->get('department.type.list', []);

		return $this;
	}

	public function importDepartments(int $offset): ImportDepartments
	{
		$sql = "SELECT * FROM `gibbonDepartment` LIMIT " . $offset . "," . $this->getLimit();

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$importRows = $stmt->fetchAll();

		if (count($importRows) < 1)
		{
			$this->offset = 0;

			return $this;
		}

		foreach ($importRows as $importRow)
		{
			$entity = $this->getDepartment($importRow);

			$entity->setBlurb(null);

			foreach ($importRow as $name => $value)
			{
				switch ($name)
				{
					case 'gibbonDepartmentID':
						$entity->setImportIdentifier(intval($value));
						break;
					case 'type':
						$entity->setType($value == 'Learning Area' ? 'Learning Area' : 'Administration');
						break;
					case 'name':
						$entity->setName($value);
						break;
					case 'nameShort':
						$entity->setNameShort($value);
						break;
					case 'logo':
						if (!empty($value))
							$entity->setLogo($value);
						break;
					case 'subjectListing':
					case 'blurb':
						if (!empty($value))
						{
							$exists = trim($entity->getBlurb());
							$value  = trim(str_replace($value, '', $exists) . ' ' . $value);
							$entity->setBlurb($value);
						}
						break;
					default:
						//dump([$name, $value]);
				}

			}
			$this->getDefaultManager()->persist($entity);
			$this->getDefaultManager()->flush();
//			$this->getMessages()->addMessage('warning', 'family.import.failed', ['%{id}' => $entity->getImportIdentifier()]);
			ini_set('max_execution_time', '10');
		}
		$this->offset = $offset + count($importRows);

		return $this;
	}

	/**
	 * @param $data
	 *
	 * @return Family
	 */
	private function getDepartment($data): Department
	{
		$entity = $this->getDefaultManager()->getRepository(Department::class)->findOneByImportIdentifier(intval($data['gibbonDepartmentID']));

		if (!$entity instanceof Department)
			$entity = new Department();

		return $entity;
	}

	/**
	 * @return int
	 */
	public function getCount(): ?int
	{
		return $this->count;
	}

	/**
	 * @return int
	 */
	public function getOffset(): int
	{
		if (empty($this->offset))
			$this->offset = 0;

		return $this->offset;
	}
}