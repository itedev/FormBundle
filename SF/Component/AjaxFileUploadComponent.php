<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\Component;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class AjaxFileUploadComponent
 * @package ITE\FormBundle\SF\Component
 */
class AjaxFileUploadComponent extends Component
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode, ContainerBuilder $container)
    {
        $node = parent::addConfiguration($rootNode, $container);

        return $node
            ->scalarNode('web_root')->defaultValue('%kernel.root_dir%/../web')->end()
            ->scalarNode('tmp_prefix')->cannotBeEmpty()->isRequired()->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter('ite_form.file_manager.web_root', $config['web_root']);
        $container->setParameter('ite_form.file_manager.tmp_prefix', $config['tmp_prefix']);

        parent::loadConfiguration($loader, $config, $container);
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'ajax_file_upload';
    }

}