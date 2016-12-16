<?php
namespace Busybee\PersonBundle\Model;


use Busybee\SystemBundle\Setting\SettingManager;

class PersonManager
{
    /**
     * @var SettingManager
     */
    private static $sm;

    /**
     * PersonManager constructor.
     * @param SettingManager $sm
     */
    public function __construct(SettingManager $sm)
    {
        self::$sm = $sm ;
    }

    /**
     * @return array
     */
    public function getTitles()
    {
        return self::$sm->get('Person.TitleList');
    }

    /**
     * @return array
     */
    public function getGenders()
    {
        return self::$sm->get('Person.GenderList');
    }
}