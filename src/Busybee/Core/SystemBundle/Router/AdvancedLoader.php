<?php

namespace Busybee\Core\SystemBundle\Router;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;

class AdvancedLoader extends Loader
{
	/**
	 * @var bool
	 */
	private $loaded = false;

	/**
	 * @var string
	 */
	private $path;

	/**
	 * @var array
	 */
	private $bundles;

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

		foreach ($this->bundles as $name => $bundle)
		{
			if (($bundle['active'] && !empty($bundle['route'])) || ($bundle['type'] === 'core' && !empty($bundle['route'])))
			{
				$route          = $bundle['route'];
				$importedRoutes = null;
				$importedRoutes = $this->import('@' . $route['resource'], empty($route['type']) ? 'yaml' : $route['type']);
				$importedRoutes->addPrefix(empty($route['prefix']) ? '' : $route['prefix']);
				$routes->addCollection($importedRoutes);
			}
		}

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

	/**
	 * AdvancedLoader constructor.
	 *
	 * @param Kernel $kernel
	 */
	public function __construct(Kernel $kernel)
	{
		$this->path = $kernel->getProjectDir();
		$this->bundles = [];

		if (file_exists($this->path . '/app/config/bundles.yml'))
			$this->bundles = Yaml::parse(file_get_contents($this->path . '/app/config/bundles.yml'));
	}
}