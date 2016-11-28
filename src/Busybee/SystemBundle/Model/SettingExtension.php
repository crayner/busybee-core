<?php

namespace Busybee\SystemBundle\Model ;

use Busybee\FormBundle\Model\FormErrorsParser ;
use Symfony\Component\Form\FormError ;
use Symfony\Component\Form\FormInterface ;
use Busybee\SystemBundle\Setting\SettingManager ;
use Symfony\Component\DependencyInjection\ContainerInterface as Container ;

class SettingExtension extends \Twig_Extension
{
    /**
     * @var Setting Manager
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
     * Main Twig extension. Call this in Twig to get formatted output of your form errors.
     * Note that you have to provide form as Form object, not FormView.
     * 
     * @param   FormInterface  $form
     * @param   string         $tag    The html tag, in which all errors will be packed. If you provide 'li',
     *                                 'ul' wrapper will be added
     * @param   string         $class  Class of each error. Default is none.
     * @return  string
     */
    public function getSetting($name, $default = null, $options = array())
    {
        return $this->sm->get($name, $default, $options);
    }

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

    public function getMenu()
    {
        return $this->container->get('menu.manager')->getMenu();
    }

    public function getMenuItems($node)
    {
        return $this->container->get('menu.manager')->getMenuItems($node);
    }

    public function testMenuItem($test)
    {
        return $this->container->get('menu.manager')->testMenuItem($test);
    }

    public function menuRequired($menu)
    {	
		return $this->container->get('menu.manager')->menuRequired($menu);
    }

    public function getName()
    {
        return 'system_twig_extension';
    }
}