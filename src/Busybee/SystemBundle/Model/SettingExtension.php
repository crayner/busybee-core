<?php

namespace Busybee\SystemBundle\Model ;

use Busybee\HomeBundle\Model\MenuManager;
use Busybee\SystemBundle\Setting\SettingManager ;

class SettingExtension extends \Twig_Extension
{
    /**
     * @var SettingManager
     */
    private $sm ;

    /**
     * @var MenuManager
     */
    private $menuManager;

    public function __construct(SettingManager $sm, MenuManager $menuManager, $parameters)
    {
        $this->sm = $sm;
        $this->menuManager = $menuManager;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_setting', array($this->sm, 'get')),
            new \Twig_SimpleFunction('get_parameter', array($this, 'getParameter')),
            new \Twig_SimpleFunction('get_menu', array($this, 'getMenu')),
            new \Twig_SimpleFunction('get_menuItems', array($this, 'getMenuItems')),
            new \Twig_SimpleFunction('test_menuItem', array($this, 'testMenuItem')),
            new \Twig_SimpleFunction('menu_required', array($this, 'menuRequired')),
            new \Twig_SimpleFunction('array_flip', array($this, 'arrayFlip')),
        );
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getParameter($name, $default = null)
    {
        if (strpos($name, '.') === false)
            return $this->parameters->get($name, $default);
        $name = explode('.', $name);
        $value = $this->parameters->get($name[0]);
        array_shift($name);
        while (! empty($name)) {
            $key = reset($name);
            $value = $value[$key];
            array_shift($name);
        }
        return $value;
    }

    /**
     * @return mixed
     */
    public function getMenu()
    {
        return $this->menuManager->getMenu();
    }

    /**
     * @param $node
     * @return mixed
     */
    public function getMenuItems($node)
    {
        return $this->menuManager->getMenuItems($node);
    }

    /**
     * @param $test
     * @return bool
     */
    public function testMenuItem($test)
    {
        return $this->menuManager->testMenuItem($test);
    }

    /**
     * @param $menu
     * @return bool
     */
    public function menuRequired($menu)
    {
        return $this->menuManager->menuRequired($menu);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'system_twig_extension';
    }

    public function arrayFlip($data)
    {
        return array_flip($data);
    }
}