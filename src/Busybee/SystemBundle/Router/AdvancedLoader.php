<?php
namespace Busybee\SystemBundle\Router ;

use Symfony\Component\Config\Loader\Loader ;
use Symfony\Component\Routing\Route ;
use Symfony\Component\Yaml\Yaml ;
use Symfony\Component\Routing\RouteCollection ;
use DirectoryIterator ;

class AdvancedLoader extends Loader
{
    private $loaded = false;

    public function load($resource, $type = null)
    {
        if ($this->loaded)
            throw new \RuntimeException('Do not add the "advanced" loader twice');

        $routes = new RouteCollection();

		foreach (new DirectoryIterator(__DIR__.'/../../Plugin') as $fileInfo) 
		{
			if ($fileInfo->isDot()) continue;
			if ($fileInfo->isDir())
			{
				$bundle = str_replace('Bundle', '', $fileInfo->getFileName());
				$content = Yaml::parse(file_get_contents($fileInfo->getRealPath().'/Resources/config/services.yml'));
				$route = $content['parameters'][$bundle]['Route'];
				$resource = '@'.$route['resource'];
				
				$importedRoutes = $this->import('@'.$route['resource'], $route['type']);
				
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
}