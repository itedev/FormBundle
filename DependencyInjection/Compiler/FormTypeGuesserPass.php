<?php

namespace ITE\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class FormTypeGuesserPass
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeGuesserPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ite_form.form.type_guesser')) {
            return;
        }

        $definition = $container->getDefinition('ite_form.form.type_guesser');
        $serviceIds = array_keys($container->findTaggedServiceIds('form.type_guesser'));
        $serviceIds = array_filter($serviceIds, function ($serviceId) {
            return $serviceId !== 'ite_form.form.type_guesser';
        });

        $serviceReferences = array_map(function ($serviceId) {
            return new Reference($serviceId);
        }, $serviceIds);

        $definition->replaceArgument(2, $serviceReferences);
    }
}
