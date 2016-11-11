<?php

namespace Busybee\HomeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

trait MenuExtension
{
    public function buildMenu($dir, ContainerBuilder $container)
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
			->set('nodes', array_merge($menu, $newContainer->getParameterBag()->get('nodes')));

		if ($container->getParameterBag()->has('items')) 
		{
			$menu =  $container->getParameterBag()->get('items', array()) ;
			$container->getParameterBag()->remove('items');
		} else
			$menu = array();
		
		$container->getParameterBag()
			->set('items', array_merge($menu, $newContainer->getParameterBag()->get('items')));
			
		return $container ;
    }
}
