<?php

namespace Busybee\ActivityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

trait ActivityExtension
{
    /**
     * @param $dir
     * @param ContainerBuilder $container
     */
    public function activityTabs($dir, ContainerBuilder $container)
    {
        $newContainer = new ContainerBuilder();
        $loader = new Loader\YamlFileLoader($newContainer, new FileLocator($dir . '/../Resources/config/activity'));
        $loader->load('parameters.yml');

        if ($container->getParameterBag()->has('activity')) {
            $activity = $container->getParameterBag()->get('activity', []);
            $container->getParameterBag()->remove('activity');
        } else
            $activity = [];

        $container->getParameterBag()
            ->set('activity', $this->activityTabMerge($activity, $newContainer->getParameterBag()->get('activity')));

        return $container;
    }

    /**
     * @param $a1
     * @param $a2
     * @return mixed
     */
    protected function activityTabMerge($a1, $a2)
    {
        foreach ($a2 as $q => $w)
            if (!array_key_exists($q, $a1))
                $a1[$q] = $w;

        return $a1;
    }
}
