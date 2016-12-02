<?php

namespace Busybee\InstituteBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

trait CalendarExtension
{
    public function calendarTabs($dir, ContainerBuilder $container)
    {
		$newContainer =  new ContainerBuilder();
		$loader = new Loader\YamlFileLoader($newContainer, new FileLocator($dir.'/../Resources/config/calendar'));
		$loader->load('parameters.yml');

		if ($container->getParameterBag()->has('calendar')) 
		{
			$calendar =  $container->getParameterBag()->get('calendar', array()) ;
			$container->getParameterBag()->remove('calendar');
		} else
			$calendar = array();
		
		$container->getParameterBag()
			->set('calendar', $this->calendarTabMerge($calendar, $newContainer->getParameterBag()->get('calendar')));
    }
	
	protected function calendarTabMerge($a1, $a2)
	{
		foreach($a2 as $q=>$w)
			if (! array_key_exists($q, $a1))
				$a1[$q] = $w;
		
		return $a1;
	}
}
