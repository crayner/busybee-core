<?php

namespace GibbonBundle\Model;

use Busybee\People\PersonBundle\Model\PersonManager;
use Doctrine\Common\Persistence\ObjectManager;

class ImportGrades extends ImportManager
{
	/**
	 * @var int
	 */
	private $count = 0;

	/**
	 * @var int
	 */
	private $offset;

	/**
	 * @var int
	 */
	private $gibbonSchoolYearID;

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

		$sql = "SELECT `gibbonSchoolYearID` FROM `gibbonSchoolYear` WHERE `status` = 'Current'";

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$this->gibbonSchoolYearID = $stmt->fetch();

		return $this;
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