<?php

namespace Busybee\SystemBundle\Model ;

use Symfony\Component\Form\FormInterface ;
use Busybee\SystemBundle\Setting\SettingManager ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container ;

class SettingExtension extends \Twig_Extension
{
    /**
     * @var SettingManager
     */
    private $sm ;
	
    /**
     * @var Container
     */
    private $container ;
	
    public function __construct(SettingManager $sm, Container $container)
    {
        $this->sm = $sm;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_setting', array($this, 'getSetting')),
            new \Twig_SimpleFunction('get_parameter', array($this, 'getParameter')),
            new \Twig_SimpleFunction('get_menu', array($this, 'getMenu')),
            new \Twig_SimpleFunction('get_menuItems', array($this, 'getMenuItems')),
            new \Twig_SimpleFunction('test_menuItem', array($this, 'testMenuItem')),
            new \Twig_SimpleFunction('menu_required', array($this, 'menuRequired')),
        );
    }

    /**
     * @param $name
     * @param null $default
     * @param array $options
     * @return mixed
     */
    public function getSetting($name, $default = null, $options = array())
    {
        return $this->sm->get($name, $default, $options);
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed
     */
    public function getParameter($name, $default = null)
    {
        if (strpos($name, '.') === false)
			return $this->container->getParameter($name, $default);
		$name = explode('.', $name);
		$value = $this->container->getParameter($name[0]);
		array_shift($name);
		while (! empty($name))
		{
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
        return $this->container->get('menu.manager')->getMenu();
    }

    /**
     * @param $node
     * @return mixed
     */
    public function getMenuItems($node)
    {
        return $this->container->get('menu.manager')->getMenuItems($node);
    }

    /**
     * @param $test
     * @return bool
     */
    public function testMenuItem($test)
    {
        return $this->container->get('menu.manager')->testMenuItem($test);
    }

    /**
     * @param $menu
     * @return bool
     */
    public function menuRequired($menu)
    {	
		return $this->container->get('menu.manager')->menuRequired($menu);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'system_twig_extension';
    }
}