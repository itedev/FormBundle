<?php

namespace ITE\FormBundle\DependencyInjection;

use ITE\FormBundle\SF\ExtensionInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class Configuration
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @param ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ite_form');

        $this->addComponentsConfiguration($rootNode);
        $this->addPluginsConfiguration($rootNode);
        $this->addTypeOptionsConfiguration($rootNode);

        $rootNode
            ->children()
                ->scalarNode('timezone')->defaultValue(date_default_timezone_get())->end()
            ->end()
        ->end();

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addComponentsConfiguration(ArrayNodeDefinition $rootNode)
    {
        $componentsNode = $rootNode
            ->children()
                ->arrayNode('components')
                    ->canBeUnset()
                    ->addDefaultsIfNotSet();

        $serviceIds = $this->container->findTaggedServiceIds('ite_form.component');
        foreach ($serviceIds as $serviceId => $tags) {
            /** @var $component ExtensionInterface */
            $component = $this->container->get($serviceId);
            $componentNode = $component->addConfiguration($this->container);
            $componentsNode->append($componentNode);
        }
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addPluginsConfiguration(ArrayNodeDefinition $rootNode)
    {
        $pluginsNode = $rootNode
            ->children()
                ->arrayNode('plugins')
                    ->canBeUnset()
                    ->addDefaultsIfNotSet();

        $serviceIds = $this->container->findTaggedServiceIds('ite_form.plugin');
        foreach ($serviceIds as $serviceId => $tags) {
            /** @var $plugin ExtensionInterface */
            $plugin = $this->container->get($serviceId);
            $pluginNode = $plugin->addConfiguration($this->container);
            $pluginsNode->append($pluginNode);
        }
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addTypeOptionsConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('type_options')
                    ->defaultValue([])
                    ->useAttributeAsKey('name')
                    ->prototype('variable')
                ->end()
        ;
    }
}
