<?php

namespace ITE\FormBundle\DependencyInjection\Compiler\Component\Validation;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ConstraintPass
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ConstraintPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ite_form.validation.constraint_manager')) {
            return;
        }

        $definition = $container->getDefinition('ite_form.validation.constraint_manager');

        $serviceIds = $container->findTaggedServiceIds('ite_form.validation.constraint_converter');
        foreach ($serviceIds as $serviceId => $tagAttributes) {
            $definition->addMethodCall('addConverter', [new Reference($serviceId)]);
        }

        $serviceIds = $container->findTaggedServiceIds('ite_form.validation.constraint_processor');
        foreach ($serviceIds as $serviceId => $tagAttributes) {
            $definition->addMethodCall('addProcessor', [new Reference($serviceId)]);
        }

        $serviceIds = $container->findTaggedServiceIds('ite_form.validation.constraint_transformer');
        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addTransformer', [$attributes['alias'], new Reference($serviceId)]);
            }
        }
    }
}
