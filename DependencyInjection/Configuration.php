<?php

namespace ITE\FormBundle\DependencyInjection;

use Doctrine\Common\Inflector\Inflector;
use ITE\FormBundle\Components;
use ITE\FormBundle\SF\SFFormExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ite_form');

        // add components configuration
        $this->addComponentsConfiguration($rootNode);

        // add plugins configuration
        $this->addPluginsConfiguration($rootNode);

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
                    ->canBeUnset();

        foreach (Components::$components as $component) {
            // add common component configuration
            $componentNode = $this->addComponentConfiguration($component, $componentsNode);

            // load specific component configuration
            $method = 'add' . Inflector::classify($component) . 'ComponentConfiguration';
            if (method_exists($this, $method)) {
                $this->$method($componentNode);
            }
        }
    }

    /**
     * @param $component
     * @param ArrayNodeDefinition $componentsNode
     * @return NodeBuilder
     */
    private function addComponentConfiguration($component, ArrayNodeDefinition $componentsNode)
    {
        /** @var $componentNode NodeBuilder */
        $componentNode = $componentsNode
            ->children()
                ->arrayNode($component)
                    ->canBeUnset()
                    ->addDefaultsIfNotSet()
                    ->treatNullLike(array('enabled' => true))
                    ->treatTrueLike(array('enabled' => true))
                    ->children();

        $componentNode
          ->booleanNode('enabled')->defaultFalse()->end();

//        $componentNode
//                    ->end()
//                ->end()
//            ->end()
//        ;

        return $componentNode;
    }

    /**
     * @param NodeBuilder $componentNode
     */
    private function addAjaxFileUploadComponentConfiguration(NodeBuilder $componentNode)
    {
        $componentNode
            ->scalarNode('web_root')->defaultValue('%kernel.root_dir%/../web')->end()
            ->scalarNode('tmp_prefix')->cannotBeEmpty()->isRequired()->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addPluginsConfiguration(ArrayNodeDefinition $rootNode)
    {
        $pluginsNode = $rootNode
            ->children()
                ->arrayNode('plugins')
                    ->canBeUnset();

        foreach (SFFormExtension::getPlugins() as $plugin) {
            // add common plugin configuration
            $pluginNode = $this->addPluginConfiguration($plugin, $pluginsNode);

            // load specific plugin configuration
            $method = 'add' . Inflector::classify($plugin) . 'PluginConfiguration';
            if (method_exists($this, $method)) {
                $this->$method($pluginNode);
            }
        }
    }

    /**
     * @param $plugin
     * @param ArrayNodeDefinition $pluginsNode
     * @return NodeBuilder
     */
    private function addPluginConfiguration($plugin, ArrayNodeDefinition $pluginsNode)
    {
        /** @var $pluginNode NodeBuilder */
        $pluginNode = $pluginsNode
            ->children()
                ->arrayNode($plugin)
                    ->canBeUnset()
                    ->addDefaultsIfNotSet()
                    ->treatNullLike(array('enabled' => true))
                    ->treatTrueLike(array('enabled' => true))
                    ->children();

        $pluginNode
            ->booleanNode('enabled')->defaultFalse()->end()
            ->variableNode('options')->defaultValue(array())->end();

//        $pluginNode
//                    ->end()
//                ->end()
//            ->end()
//        ;

        return $pluginNode;
    }

    /**
     * @param NodeBuilder $pluginNode
     */
    private function addFileuploadPluginConfiguration(NodeBuilder $pluginNode)
    {
        $pluginNode
            ->variableNode('file_manager')->defaultValue(array())->end();
        ;
    }
}
