<?php

namespace Busybee\Core\CalendarBundle\Model;

use Busybee\Core\CalendarBundle\Repository\YearRepository;
use Busybee\SecurityBundle\Doctrine\UserManager;
use Busybee\Core\CalendarBundle\Entity\Year;
use Busybee\SecurityBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CurrentYear
{
	/**
	 * @var Year
	 */
	private $currentYear;

	/**
	 * CurrentYear constructor.
	 *
	 * @param UserManager  $um
	 * @param TokenStorage $ts
	 */
	public function __construct(UserManager $um, TokenStorage $ts, YearRepository $yr)
	{
		if (!is_null($ts->getToken()) && ($ts->getToken()->getUser() instanceof UserInterface))
			$this->currentYear = $um->getSystemYear($ts->getToken()->getUser());
		else
			$this->currentYear = $yr->findCurrentYear();
	}

	/**
	 * Get Current Year
	 *
	 * @return Year
	 */
	public function getCurrentYear()
	{
		return $this->currentYear;
	}
}