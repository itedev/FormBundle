<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\FormBundle\SF\Plugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class TypeaheadPlugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TypeaheadPlugin extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode, ContainerBuilder $container)
    {
        $node = parent::addConfiguration($rootNode, $container);

        return $node
            ->variableNode('dataset_options')
                ->defaultValue([])
            ->end()
            ->variableNode('engine_options')
                ->defaultValue([])
            ->end()            
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('ite_form.plugin.%s.dataset_options', static::getName()), $config['dataset_options']);
        $container->setParameter(sprintf('ite_form.plugin.%s.engine_options', static::getName()), $config['engine_options']);
        parent::loadConfiguration($loader, $config, $container);
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'typeahead';
    }
}
