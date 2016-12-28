<?php

namespace Busybee\PersonBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

trait StudentExtension
{
    public function studentTabs($dir, ContainerBuilder $container)
    {
		$newContainer =  new ContainerBuilder();
		$loader = new Loader\YamlFileLoader($newContainer, new FileLocator($dir.'/../Resources/config/student'));
		$loader->load('parameters.yml');

		if ($container->getParameterBag()->has('student')) 
		{
			$student =  $container->getParameterBag()->get('student', array()) ;
			$container->getParameterBag()->remove('student');
		} else
			$student = array();
		
		$container->getParameterBag()
			->set('student', $this->studentTabMerge($student, $newContainer->getParameterBag()->get('student')));

        return $container ;
    }
	
	protected function studentTabMerge($a1, $a2)
	{
		foreach($a2 as $q=>$w)
			if (! array_key_exists($q, $a1))
				$a1[$q] = $w;
		
		return $a1;
	}
}
