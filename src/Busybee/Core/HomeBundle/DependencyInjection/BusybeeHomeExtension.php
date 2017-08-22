<?php

namespace Busybee\Core\HomeBundle\DependencyInjection;

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

	use \Busybee\Core\HomeBundle\DependencyInjection\MenuExtension;

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
		$container->setParameter(
			'author',
			'Craig Rayner'
		);

		$configuration = new Configuration();
		$config        = $this->processConfiguration($configuration, $configs);

		$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
		$loader->load('services.yml');

		$container = $this->buildMenu(__DIR__, $container);
	}
}
