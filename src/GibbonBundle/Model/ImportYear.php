<?php

namespace GibbonBundle\Model;

use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\People\PersonBundle\Model\PersonManager;
use Doctrine\Common\Persistence\ObjectManager;

class ImportYear extends ImportManager
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
	 * ImportPeople constructor.
	 *
	 * @param ObjectManager $gibbonManager
	 * @param ObjectManager $manager
	 * @param PersonManager $personManager
	 */
	public function __construct(ObjectManager $gibbonManager, ObjectManager $manager, PersonManager $personManager)
	{
		parent::__construct($gibbonManager, $manager, $personManager);

		$sql = "SELECT * FROM `gibbonSchoolYear` WHERE `status` = 'Current'";

		$stmt = $this->getGibbonManager()->getConnection()->prepare($sql);
		$stmt->execute();
		$this->result = $stmt->fetch();

		$this->year = $manager->getRepository(Year::class)->findOneByStatus('current');

		if ($this->year instanceof Year)
		{
			$this->year->setName($this->result['name']);
			$this->year->setFirstDay(new \DateTime($this->result['firstDay']));
			$this->year->setLastDay(new \DateTime($this->result['lastDay']));
			$this->year->setImportIdentifier($this->result['gibbonSchoolYearID']);

			try
			{
				$manager->persist($this->year);
				$manager->flush();
			}
			catch (\Exception $e)
			{
				$this->getMessages()->addMessage('danger', 'The year ' . $this->year->getName() . ' failed to transfer. ');

				return $this;
			}
		}

		$this->getMessages()->addMessage('success', 'The year ' . $this->year->getName() . ' has been transferred. ');

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