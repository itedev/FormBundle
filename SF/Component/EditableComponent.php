<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\AbstractComponent;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class EditableComponent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class EditableComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ContainerBuilder $container)
    {
        $rootNode = parent::addConfiguration($container);
        $rootNode
            ->children()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('route')
                            ->cannotBeEmpty()
                            ->defaultValue('ite_form_component_editable_edit')
                        ->end()
                        ->variableNode('route_parameters')
                            ->defaultValue([])
                        ->end()
                        ->scalarNode('template')
                            ->cannotBeEmpty()
                            ->defaultValue('ITEFormBundle:Form/Component/editable:field.html.twig')
                        ->end()
                        ->variableNode('formatter_options')
                            ->defaultValue([])
                        ->end()
                        ->variableNode('form_options')
                            ->defaultValue([])
                        ->end()
                        ->variableNode('field_options')
                            ->defaultValue([])
                        ->end()
                    ->end()
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
        $container->setParameter('ite_form.component.editable.defaults', $config['defaults']);

        parent::loadConfiguration($loader, $config, $container);
    }

    /**
     * {@inheritdoc}
     */
    public function getStylesheets()
    {
        return [
            '@ITEFormBundle/Resources/public/css/component/Editable/editable.css',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        return [
            '@ITEFormBundle/Resources/public/js/component/Editable/jquery.editable.js',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'editable';
    }
}
