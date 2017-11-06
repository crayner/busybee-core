<?php
namespace GibbonBundle\Model;

use Busybee\People\PersonBundle\Model\PersonManager;
use Doctrine\Common\Persistence\ObjectManager;

abstract class ImportManager
{
	/**
	 * @var ObjectManager
	 */
	private $gibbonManager;
	/**
	 * @var ObjectManager
	 */
	private $manager;
	/**
	 * @var PersonManager
	 */
	private $personManager;

	/**
	 * @var int
	 */
	private $limit = 200;

	/**
	 * ImportManager constructor.
	 *
	 * @param ObjectManager $gibbonManager
	 * @param ObjectManager $manager
	 * @param PersonManager $personManager
	 */
	public function __construct(ObjectManager $gibbonManager, ObjectManager $manager, PersonManager $personManager)
	{
		$this->gibbonManager = $gibbonManager;
		$this->manager       = $manager;
		$this->personManager = $personManager;
	}

	/**
	 * @return ObjectManager
	 */
	public function getDefaultManager(): ObjectManager
	{
		return $this->manager;
	}

	/**
	 * @return ObjectManager
	 */
	public function getGibbonManager(): ObjectManager
	{
		return $this->gibbonManager;
	}

	/**
	 * @return PersonManager
	 */
	public function getPersonManager(): PersonManager
	{
		return $this->personManager;
	}

	/**
	 * @return int
	 */
	public function getLimit(): int
	{
		return $this->limit;
	}
}