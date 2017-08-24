<?php

namespace Busybee\People\FamilyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

trait FamilyExtension
{
	/**
	 * @param string           $dir
	 * @param ContainerBuilder $container
	 *
	 * @return ContainerBuilder
	 */
	public function familyTabs($dir, ContainerBuilder $container)
	{
		$newContainer = new ContainerBuilder();
		$loader       = new Loader\YamlFileLoader($newContainer, new FileLocator($dir . '/../Resources/config/family'));
		$loader->load('parameters.yml');

		if ($container->getParameterBag()->has('family'))
		{
			$family = $container->getParameterBag()->get('family', array());
			$container->getParameterBag()->remove('family');
		}
		else
			$family = array();

		$container->getParameterBag()
			->set('family', $this->familyTabMerge($family, $newContainer->getParameterBag()->has('family') ? $newContainer->getParameterBag()->get('family') : array()));

		return $container;
	}

	/**
	 * @param array $a1
	 * @param array $a2
	 *
	 * @return mixed
	 */
	protected function familyTabMerge($a1, $a2)
	{
		foreach ($a2 as $q => $w)
			if (!array_key_exists($q, $a1))
				$a1[$q] = $w;

		return $a1;
	}
}