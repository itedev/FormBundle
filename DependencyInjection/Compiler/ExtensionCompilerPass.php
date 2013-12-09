<?php

namespace ITE\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ExtensionCompilerPass
 * @package ITE\FormBundle\DependencyInjection\Compiler
 */
class ExtensionCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ite_form.sf.extension.form')) {
            return;
        }

        $sfFormDefinition = $container->getDefinition('ite_form.sf.extension.form');
        $this->processComponents($sfFormDefinition, $container);
        $this->processPlugins($sfFormDefinition, $container);
    }

    /**
     * @param Definition $sfFormDefinition
     * @param ContainerBuilder $container
     */
    protected function processComponents(Definition $sfFormDefinition, ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('ite_form.component');
        foreach ($serviceIds as $serviceId => $attributes) {
            $sfFormDefinition->addMethodCall('addComponent', array(new Reference($serviceId)));
        }
    }

    /**
     * @param Definition $sfFormDefinition
     * @param ContainerBuilder $container
     */
    protected function processPlugins(Definition $sfFormDefinition, ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('ite_form.plugin');
        foreach ($serviceIds as $serviceId => $attributes) {
            $sfFormDefinition->addMethodCall('addPlugin', array(new Reference($serviceId)));
        }
    }
}
