<?php
namespace Busybee\DatabaseBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Database EntityListenerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('database.listener.resolver');
        $services = $container->findTaggedServiceIds('database.entity_listener');

        foreach ($services as $service => $attributes) {
            $definition->addMethodCall(
                'addMapping',
                array($container->getDefinition($service)->getClass(), $service)
            );
        }
    }
}