<?php

namespace Busybee\Core\InstallBundle\Router;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class AdvancedLoader extends Loader
{
	/**
	 * @var bool
	 */
	private $loaded = false;

	/**
	 * @param mixed $resource
	 * @param null  $type
	 *
	 * @return RouteCollection
	 */
	public function load($resource, $type = null)
	{
		if ($this->loaded)
			throw new \RuntimeException('Do not add the "advanced" loader twice');

		$routes = new RouteCollection();

		// prepare a new route
		$path         = '/';
		$defaults     = array(
			'_controller' => 'BusybeeInstallBundle:Install:index',
		);
		$requirements = [];
		$route        = new Route($path, $defaults, $requirements);

		// add the new route to the route collection
		$routeName = 'install_start';
		$routes->add($routeName, $route);


		$path         = '/install/mailer/';
		$defaults     = array(
			'_controller' => 'BusybeeInstallBundle:Install:mailer',
		);
		$requirements = [];
		$route        = new Route($path, $defaults, $requirements);

		// add the new route to the route collection
		$routeName = 'install_check_mailer_parameters';
		$routes->add($routeName, $route);


		$path         = '/install/miscellaneous/';
		$defaults     = array(
			'_controller' => 'BusybeeInstallBundle:Install:miscellaneous',
		);
		$requirements = [];
		$route        = new Route($path, $defaults, $requirements);

		// add the new route to the route collection
		$routeName = 'install_misc_check';
		$routes->add($routeName, $route);


		$path         = '/system/install/build/';
		$defaults     = array(
			'_controller' => 'SystemBundle:Install:build',
		);
		$requirements = [];
		$route        = new Route($path, $defaults, $requirements);

		// add the new route to the route collection
		$routeName = 'install_build_system';
		$routes->add($routeName, $route);


		$path         = '/install/bundles/';
		$defaults     = [
			'_controller' => 'BusybeeInstallBundle:Install:bundles',
		];
		$requirements = [];
		$route        = new Route($path, $defaults, $requirements);

		// add the new route to the route collection
		$routeName = 'install_bundles';
		$routes->add($routeName, $route);


		$this->loaded = true;

		return $routes;
	}

	/**
	 * @param mixed  $resource
	 * @param string $type
	 *
	 * @return bool
	 */
	public function supports($resource, $type = null)
	{
		return 'advanced_extra' === $type;
	}
}