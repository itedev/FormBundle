<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\AbstractComponent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class AjaxFileUploadComponent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxFileUploadComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ContainerBuilder $container)
    {
        $rootNode = parent::addConfiguration($container);
        $rootNode
            ->children()
                ->scalarNode('web_root')
                    ->defaultValue('%kernel.root_dir%/../web')
                ->end()
                ->scalarNode('tmp_prefix')
                    ->cannotBeEmpty()
                    ->isRequired()
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
