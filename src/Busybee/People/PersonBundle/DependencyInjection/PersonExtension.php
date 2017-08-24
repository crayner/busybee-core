<?php

namespace Busybee\People\PersonBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

trait PersonExtension
{
	public function personTabs($dir, ContainerBuilder $container)
	{
		$newContainer = new ContainerBuilder();
		$loader       = new Loader\YamlFileLoader($newContainer, new FileLocator($dir . '/../Resources/config/person'));
		$loader->load('parameters.yml');

		if ($container->getParameterBag()->has('person'))
		{
			$person = $container->getParameterBag()->get('person', array());
			$container->getParameterBag()->remove('person');
		}
		else
			$person = array();

		$container->getParameterBag()
			->set('person', $this->personTabMerge($person, $newContainer->getParameterBag()->get('person')));

		return $container;
	}

	protected function personTabMerge($a1, $a2)
	{
		foreach ($a2 as $q => $w)
			if (!array_key_exists($q, $a1))
				$a1[$q] = $w;

		return $a1;
	}
}
