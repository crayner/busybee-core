<?php

namespace Busybee\People\PersonBundle\Model;

use Busybee\People\PersonBundle\Entity\Person;
use Busybee\SecurityBundle\Doctrine\UserManager;
use Busybee\SecurityBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class PreferenceManager
{
	/**
	 * @var User
	 */
	private $user;

	/**
	 * @var UserManager
	 */
	private $um;

	/**
	 * System Locale
	 *
	 * @var string
	 */
	private $locale;

	/**
	 * @var ObjectManager
	 */
	private $om;

	/**
	 * PreferenceManager constructor.
	 *
	 * @param User          $user
	 * @param UserManager   $um
	 * @param               $locale
	 * @param ObjectManager $om
	 */
	public function __construct(User $user, UserManager $um, $locale, ObjectManager $om)
	{
		$this->user   = $user;
		$this->um     = $um;
		$this->locale = $locale;
		$this->om     = $om;
	}

	/**
	 * Get Person
	 *
	 * @return Person
	 */
	public function getPerson(): Person
	{
		return $this->user->getPerson();
	}

	/**
	 * Handle Request
	 *
	 * @param Request $request
	 */
	public function handleRequest(Request $request)
	{
		$pref = $request->get('person_preference');

		if (is_null($pref))
			return;

		// Handle User Settings:
		//Language
		$language = empty($pref['language']) ? $this->getLocale() : $pref['language'];
		$this->user->setLocale($language);

		$this->um->updateUser($this->user);
		$this->um->getSession()->set('_locale', $this->user->getLocale());
	}

	/**
	 * Get Locale
	 *
	 * @return string
	 */
	public function getLocale(): string
	{
		return $this->locale;
	}

	/**
	 * Get Person
	 *
	 * @return Person
	 */
	public function getUser(): User
	{
		return $this->user;
	}
}