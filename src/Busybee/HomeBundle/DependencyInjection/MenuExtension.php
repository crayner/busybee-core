<?php

namespace Busybee\HomeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

trait MenuExtension
{
    public function buildMenu($dir, ContainerBuilder $container)
    {
		if (is_dir($dir . '/../Resources/config/menu') && is_file($dir . '/../Resources/config/menu/parameters.yml'))
		{
			$newContainer =  new ContainerBuilder();
			$loader = new Loader\YamlFileLoader($newContainer, new FileLocator($dir.'/../Resources/config/menu'));
			$loader->load('parameters.yml');
			
			if ($container->getParameterBag()->has('nodes')) 
			{
				$menu =  $container->getParameterBag()->get('nodes', array()) ;
				$container->getParameterBag()->remove('nodes');
			} else
				$menu = array();
			
			$container->getParameterBag()
				->set('nodes', $this->arrayMerge($menu, $newContainer->getParameterBag()->get('nodes')));
	
			if ($container->getParameterBag()->has('items')) 
			{
				$menu =  $container->getParameterBag()->get('items', array()) ;
				$container->getParameterBag()->remove('items');
			} else
				$menu = array();
			
			$container->getParameterBag()
				->set('items', $this->arrayMerge($menu, $newContainer->getParameterBag()->get('items')));
				
		}
		return $container ;
    }
	
	protected function arrayMerge($a1, $a2)
	{
		foreach($a2 as $q=>$w)
			if (! array_key_exists($q, $a1))
				$a1[$q] = $w;
		
		return $a1;
	}
}
