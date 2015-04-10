<?php

namespace ITE\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SFFromExtensionPass
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class SFFromExtensionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ite_form.sf.extension.form')) {
            return;
        }

        $definition = $container->getDefinition('ite_form.sf.extension.form');
        $this->processComponents($definition, $container);
        $this->processPlugins($definition, $container);
    }

    /**
     * @param Definition $definition
     * @param ContainerBuilder $container
     */
    protected function processComponents(Definition $definition, ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('ite_form.component');
        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $alias = $tag['alias'];
                $enabled = $container->getParameter(sprintf('ite_form.component.%s.enabled', $alias));
                if ($enabled) {
                    $definition->addMethodCall('addComponent', array($alias, new Reference($serviceId)));
                }
            }
        }
    }

    /**
     * @param Definition $definition
     * @param ContainerBuilder $container
     */
    protected function processPlugins(Definition $definition, ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('ite_form.plugin');
        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $alias = $tag['alias'];
                $enabled = $container->getParameter(sprintf('ite_form.plugin.%s.enabled', $alias));
                if ($enabled) {
                    $definition->addMethodCall('addPlugin', array($alias, new Reference($serviceId)));
                }
            }
        }
    }
}
