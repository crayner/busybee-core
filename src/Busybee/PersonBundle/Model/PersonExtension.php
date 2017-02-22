<?php

namespace Busybee\PersonBundle\Model ;

use Busybee\PersonBundle\Entity\Address;
use Busybee\PersonBundle\Entity\Person;
use Busybee\PersonBundle\Entity\Phone;
use Busybee\SystemBundle\Setting\SettingManager ;

class PersonExtension extends \Twig_Extension
{
    /**
     * @var SettingManager
     */
    private $sm ;
	
    /**
     * @var PersonManager
     */
    private $pm ;

    /**
     * @var AddressManager
     */
    private $am ;

    /**
     * @var PhoneManager
     */
    private $phoneManager;

    /**
     * PersonExtension constructor.
     *
     * @param SettingManager $sm
     * @param PersonManager $pm
     * @param AddressManager $am
     * @param PhoneManager $phoneManager
     */
    public function __construct(SettingManager $sm, PersonManager $pm, AddressManager $am, PhoneManager $phoneManager)
    {
        $this->sm = $sm;
        $this->pm = $pm;
        $this->am = $am ;
        $this->phoneManager = $phoneManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('isCareGiver', array($this, 'isCareGiver')),
            new \Twig_SimpleFunction('isStudent', array($this, 'isStudent')),
            new \Twig_SimpleFunction('isStaff', array($this, 'isStaff')),
            new \Twig_SimpleFunction('isUser', array($this, 'isUser')),
            new \Twig_SimpleFunction('canBeStaff', array($this, 'canBeStaff')),
            new \Twig_SimpleFunction('canDeleteStaff', array($this, 'canDeleteStaff')),
            new \Twig_SimpleFunction('canBeCareGiver', array($this, 'canBeCareGiver')),
            new \Twig_SimpleFunction('canDeleteCareGiver', array($this, 'canDeleteCareGiver')),
            new \Twig_SimpleFunction('canBeStudent', array($this, 'canBeStudent')),
            new \Twig_SimpleFunction('canDeleteStudent', array($this, 'canDeleteStudent')),
            new \Twig_SimpleFunction('canBeUser', array($this, 'canBeUser')),
            new \Twig_SimpleFunction('canDeleteUser', array($this, 'canDeleteUser')),
            new \Twig_SimpleFunction('formatAddress', array($this, 'formatAddress')),
            new \Twig_SimpleFunction('formatPhone', array($this, 'formatPhone')),
        );
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isCareGiver(Person $person)
    {
        return $this->pm->isCareGiver();
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isStudent(Person $person)
    {
        return $this->pm->isStudent($person);
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function isStaff(Person $person)
    {
        return $this->pm->isStaff($person);
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeStaff(Person $person)
    {
        return $this->pm->canBeStaff($person);
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canDeleteStaff(Person $person)
    {
        return $this->pm->canDeleteStaff($person);
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeCareGiver(Person $person)
    {
        return $this->pm->canBeCareGiver($person);
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canDeleteCareGiver(Person $person)
    {
        return $this->pm->canDeleteCareGiver($person);
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeStudent(Person $person)
    {
        return $this->pm->canBeStudent($person);
    }

    /**
     * @param Person $person
     * @param $parameters
     * @return bool
     */
    public function canDeleteStudent(Person $person, $parameters)
    {
        return $this->pm->canDeleteStudent($person, $parameters);
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canDeleteUser(Person $person)
    {
        return $this->pm->canDeleteUser($person);
    }

    /**
     * @param Person $person
     * @return bool
     */
    public function canBeUser(Person $person)
    {
        return $this->pm->canBeUser($person);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'person_twig_extension';
    }

    /**
     * @param Person $person
     * @return mixed
     */
    public function isUser(Person $person)
    {
        return $this->pm->isUser($person);
    }

    /**
     * @param Address $address
     * @return html
     */
    public function formatAddress(Address $address)
    {
        return $this->am->formatAddress($address);
    }

    /**
     * @param Phone $phone
     * @return html
     */
    public function formatPhone(Phone $phone)
    {
        return $this->phoneManager->formatPhone($phone);
    }
}