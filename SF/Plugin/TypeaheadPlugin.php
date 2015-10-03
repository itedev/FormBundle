<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\Common\CdnJs\CdnAssetReference;
use ITE\FormBundle\SF\AbstractPlugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class TypeaheadPlugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TypeaheadPlugin extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ContainerBuilder $container)
    {
        $rootNode = parent::addConfiguration($container);
        $rootNode
            ->children()
                ->variableNode('dataset_options')
                    ->defaultValue([])
                ->end()
                ->variableNode('engine_options')
                    ->defaultValue([])
                ->end()
            ->end()
        ;

        return $rootNode;
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
    public function getCdnName()
    {
        return 'typeahead.js';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCdnVersion()
    {
        return '0.11.1';
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnJavascripts($debug)
    {
        return [
            new CdnAssetReference(
                $this->getCdnName(),
                $this->getCdnVersion(),
                $debug ? 'typeahead.bundle.js' : 'typeahead.bundle.min.js'
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'typeahead';
    }
}
