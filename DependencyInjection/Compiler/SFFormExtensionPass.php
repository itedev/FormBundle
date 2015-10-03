<?php

namespace ITE\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SFFormExtensionPass
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class SFFormExtensionPass implements CompilerPassInterface
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
                    $definition->addMethodCall('addComponent', [$alias, new Reference($serviceId)]);
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
                    $cdnParameter = sprintf('ite_form.plugin.%s.cdn', $alias);
                    if ($container->hasParameter($cdnParameter)) {
                        $pluginDefinition = $container->getDefinition($serviceId);
                        $cdn = $container->getParameter($cdnParameter);
                        $pluginDefinition->addMethodCall('setCdn', [$cdn]);
                    }

                    $definition->addMethodCall('addPlugin', [$alias, new Reference($serviceId)]);
                }
            }
        }
    }
}
