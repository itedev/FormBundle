<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\FormBundle\SF\Plugin;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class FileuploadPlugin
 * @package ITE\FormBundle\SF\Plugin
 */
class FileuploadPlugin extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode, ContainerBuilder $container)
    {
        $node = parent::addConfiguration($rootNode, $container);

        return $node
            ->variableNode('file_manager')
                ->defaultValue(array())
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('ite_form.plugin.%s.%s', static::getName(), 'file_manager'), $config['file_manager']);

        parent::loadConfiguration($loader, $config, $container);
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'fileupload';
    }

}