<?php

namespace ITE\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class EntityConverterPass
 * @package ITE\FormBundle\DependencyInjection\Compiler
 */
class EntityConverterPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ite_form.entity_converter_manager')) {
            return;
        }

        $definition = $container->getDefinition('ite_form.entity_converter_manager');
        $serviceIds = $container->findTaggedServiceIds('ite_form.converter');
        foreach ($serviceIds as $serviceId => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall('addConverter', array($attributes['alias'], new Reference($serviceId)));
            }
        }
    }
}