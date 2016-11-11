<?php

namespace Busybee\HomeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class BusybeeHomeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
 		$container->setParameter(
            'current_year',
            date("Y")
        );

        $container->setParameter(
            'current_month',
            date("m")
        );

        $container->setParameter(
            'current_day',
            date("d")
        ); 

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
		
		$newContainer =  new ContainerBuilder();
		$loader = new Loader\YamlFileLoader($newContainer, new FileLocator(__DIR__.'/../Resources/config/menu'));
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
    }
}
