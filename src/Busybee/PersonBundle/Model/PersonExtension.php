<?php

namespace Busybee\PersonBundle\Model ;

use Busybee\PersonBundle\Entity\Person;
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
	
    public function __construct(SettingManager $sm, PersonManager $pm)
    {
        $this->sm = $sm;
        $this->pm = $pm;
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
        );
    }

    /**
     * @param Person $person
     * @return mixed
     */
    public function isCareGiver(Person $person)
    {
        return $this->pm->isCareGiver($person);
    }

    /**
     * @param Person $person
     * @return mixed
     */
    public function isStudent(Person $person)
    {
        return $this->pm->isStudent($person);
    }

    /**
     * @param Person $person
     * @return mixed
     */
    public function isStaff(Person $person)
    {
        return $this->pm->isStaff($person);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'person_twig_extension';
    }
}