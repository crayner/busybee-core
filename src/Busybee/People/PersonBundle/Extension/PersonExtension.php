<?php

namespace Busybee\People\PersonBundle\Extension;

use Busybee\People\AddressBundle\Entity\Address;
use Busybee\People\PersonBundle\Entity\Person;
use Busybee\People\PhoneBundle\Entity\Phone;
use Busybee\Core\SystemBundle\Setting\SettingManager;
use Busybee\People\AddressBundle\Model\AddressManager;
use Busybee\People\PersonBundle\Model\PersonManager;
use Busybee\People\PhoneBundle\Model\PhoneManager;

class PersonExtension extends \Twig_Extension
{
	/**
	 * @var PersonManager
	 */
	private $pm;

	/**
	 * @var AddressManager
	 */
	private $am;

	/**
	 * @var PhoneManager
	 */
	private $phoneManager;

	/**
	 * PersonExtension constructor.
	 *
	 * @param SettingManager $sm
	 * @param PersonManager  $pm
	 * @param AddressManager $am
	 * @param PhoneManager   $phoneManager
	 */
	public function __construct(PersonManager $pm, AddressManager $am, PhoneManager $phoneManager)
	{
		$this->pm           = $pm;
		$this->am           = $am;
		$this->phoneManager = $phoneManager;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFunctions()
	{
		return array(
			new \Twig_SimpleFunction('isCareGiver', array($this->pm, 'isCareGiver')),
			new \Twig_SimpleFunction('isStudent', array($this->pm, 'isStudent')),
			new \Twig_SimpleFunction('isStaff', array($this->pm, 'isStaff')),
			new \Twig_SimpleFunction('isUser', array($this->pm, 'isUser')),
			new \Twig_SimpleFunction('canBeStaff', array($this->pm, 'canBeStaff')),
			new \Twig_SimpleFunction('canDeleteStaff', array($this->pm, 'canDeleteStaff')),
			new \Twig_SimpleFunction('canBeCareGiver', array($this->pm, 'canBeCareGiver')),
			new \Twig_SimpleFunction('canDeleteCareGiver', array($this->pm, 'canDeleteCareGiver')),
			new \Twig_SimpleFunction('canBeStudent', array($this->pm, 'canBeStudent')),
			new \Twig_SimpleFunction('canDeleteStudent', array($this->pm, 'canDeleteStudent')),
			new \Twig_SimpleFunction('canBeUser', array($this->pm, 'canBeUser')),
			new \Twig_SimpleFunction('canDeleteUser', array($this->pm, 'canDeleteUser')),
			new \Twig_SimpleFunction('formatAddress', array($this->am, 'formatAddress')),
			new \Twig_SimpleFunction('formatPhone', array($this->phoneManager, 'formatPhone')),
			new \Twig_SimpleFunction('validPerson', array($this->pm, 'validPerson')),
		);
	}
}