<?php

namespace Busybee\CurriculumBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

trait CurriculumExtension
{
    public function CurriculumTabs($dir, ContainerBuilder $container)
    {
        $newContainer = new ContainerBuilder();
        $loader = new Loader\YamlFileLoader($newContainer, new FileLocator($dir . '/../Resources/config/curriculum'));
        $loader->load('parameters.yml');

        if ($container->getParameterBag()->has('curriculum')) {
            $menu = $container->getParameterBag()->get('curriculum', array());
            $container->getParameterBag()->remove('curriculum');
        } else
            $menu = array();

        $container->getParameterBag()
            ->set('curriculum', $this->CurriculumTabMerge($menu, $newContainer->getParameterBag()->get('curriculum')));
    }

    protected function CurriculumTabMerge($a1, $a2)
    {
        foreach ($a2 as $q => $w)
            if (!array_key_exists($q, $a1))
                $a1[$q] = $w;

        return $a1;
    }
}
