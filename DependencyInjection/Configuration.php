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

        $this->addClassesConfiguration($rootNode);
        $this->addComponentsConfiguration($rootNode);
        $this->addPluginsConfiguration($rootNode);
        $this->addTypesConfiguration($rootNode);
        $this->addTypeExtensionsConfiguration($rootNode);

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
    private function addClassesConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('form')
                            ->cannotBeEmpty()
                            ->defaultValue('ITE\FormBundle\Form\Form')
                        ->end()
                        ->scalarNode('form_builder')
                            ->cannotBeEmpty()
                            ->defaultValue('ITE\FormBundle\Form\Builder\FormBuilder')
                        ->end()
                        ->scalarNode('button_builder')
                            ->cannotBeEmpty()
                            ->defaultValue('ITE\FormBundle\Form\Builder\ButtonBuilder')
                        ->end()
                        ->scalarNode('submit_button_builder')
                            ->cannotBeEmpty()
                            ->defaultValue('ITE\FormBundle\Form\Builder\SubmitButtonBuilder')
                        ->end()
                    ->end()
                ->end()
        ;
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
    private function addTypesConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('types')
                    ->defaultValue([])
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('parent')
                                ->isRequired()
                            ->end()
                            ->variableNode('options')
                                ->defaultValue([])
                            ->end()
                        ->end()
                    ->end()
                ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addTypeExtensionsConfiguration(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('type_extensions')
                    ->defaultValue([])
                    ->useAttributeAsKey('name')
                    ->prototype('variable')
                ->end()
            ->end()
        ;
    }
}
