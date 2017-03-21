<?php

namespace Busybee\EnrolmentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Config\FileLocator;

trait EnrolmentExtension
{
    public function enrolmentTabs($dir, ContainerBuilder $container)
    {
        $newContainer = new ContainerBuilder();
        $loader = new Loader\YamlFileLoader($newContainer, new FileLocator($dir . '/../Resources/config/enrolment'));
        $loader->load('parameters.yml');

        if ($container->getParameterBag()->has('enrolment')) {
            $calendar = $container->getParameterBag()->get('enrolment', array());
            $container->getParameterBag()->remove('enrolment');
        } else
            $calendar = array();

        $container->getParameterBag()
            ->set('enrolment', $this->enrolmentTabMerge($calendar, $newContainer->getParameterBag()->get('enrolment')));
    }

    protected function enrolmentTabMerge($a1, $a2)
    {
        foreach ($a2 as $q => $w)
            if (!array_key_exists($q, $a1))
                $a1[$q] = $w;

        return $a1;
    }
}
