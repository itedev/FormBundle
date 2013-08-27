<?php

namespace ITE\FormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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

        $pluginsNode = $rootNode->children()->arrayNode('plugins');

        $this->addTinymce($pluginsNode);

        $rootNode
            ->children()
                ->scalarNode('timezone')->defaultValue(date_default_timezone_get())->end()
            ->end()
        ->end();

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $pluginsNode
     */
    private function addTinymce(ArrayNodeDefinition $pluginsNode)
    {
        $pluginsNode
            ->children()
                ->arrayNode('tinymce')
                    ->canBeUnset()
                    ->addDefaultsIfNotSet()
                    ->treatNullLike(array('enabled' => true))
                    ->treatTrueLike(array('enabled' => true))
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->variableNode('extras')->defaultValue(array())->end()
                        ->variableNode('options')->defaultValue(array())->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
