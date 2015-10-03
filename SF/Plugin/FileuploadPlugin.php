<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\FormBundle\SF\AbstractPlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class FileuploadPlugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FileuploadPlugin extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ContainerBuilder $container)
    {
        $rootNode = parent::addConfiguration($container);
        $rootNode
            ->children()
                ->variableNode('file_manager')
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
