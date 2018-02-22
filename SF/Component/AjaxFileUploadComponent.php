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
                ->scalarNode('upload_dir')
                    ->cannotBeEmpty()
                    ->isRequired()
                ->end()
                ->scalarNode('upload_path')
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
        $container->setParameter('ite_form.component.ajax_file_upload.upload_dir', $config['upload_dir']);
        $container->setParameter('ite_form.component.ajax_file_upload.upload_path', $config['upload_path']);

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
