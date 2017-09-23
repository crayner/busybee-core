<?php

namespace Busybee\Core\CalendarBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class BusybeeCalendarExtension extends Extension
{
	use \Busybee\Core\HomeBundle\DependencyInjection\MenuExtension;
	use \Busybee\Core\CalendarBundle\DependencyInjection\CalendarExtension;

	/**
	 * {@inheritdoc}
	 */
	public function load(array $configs, ContainerBuilder $container)
	{
//		$configuration = new Configuration();
//		$config        = $this->processConfiguration($configuration, $configs);

		$loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
		$loader->load('services.yml');

		$container = $this->buildMenu(__DIR__, $container);
		$container = $this->calendarTabs(__DIR__, $container);
	}
}
