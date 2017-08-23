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

	public function load($resource, $type = null)
	{
		if ($this->loaded)
			throw new \RuntimeException('Do not add the "advanced" loader twice');

		$routes = new RouteCollection();

		$path = realpath($this->path . '/vendor/busybee');

		if (is_dir($path))
			foreach (new \DirectoryIterator($path) as $fileInfo)
			{
				if ($fileInfo->isDot()) continue;
				if ($fileInfo->isDir())
				{
					$plugin   = str_replace('Bundle', '', $fileInfo->getFileName()) . '_plugin';
					$content  = Yaml::parse(file_get_contents($fileInfo->getRealPath() . '/src//Resources/config/services.yml'));
					$route    = $content['parameters'][$plugin]['Route'];
					$resource = '@' . $route['resource'];

					$importedRoutes = $this->import($resource, $route['type']);
					$routes->addCollection($importedRoutes);
					$routes->addPrefix($route['prefix']);
				}
			}

		$this->loaded = true;

		return $routes;
	}

	public function supports($resource, $type = null)
	{
		return 'advanced_extra' === $type;
	}

	/**
	 * AdvancedLoader constructor.
	 *
	 * @param $path
	 */
	public function __construct(Kernel $kernel)
	{
		$this->path = $kernel->getProjectDir();
	}
}