<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\Component;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class CollectionComponent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CollectionComponent extends Component
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode, ContainerBuilder $container)
    {
        /** @var $node NodeBuilder */
        $node = parent::addConfiguration($rootNode, $container);
        $node
            ->arrayNode('widget_show_animation')
                ->addDefaultsIfNotSet()
                ->info('animation for showing new collection items')
                ->children()
                    ->enumNode('type')
                        ->defaultValue('show')
                        ->values(['show', 'slide', 'fade'])
                    ->end()
                    ->integerNode('length')
                        ->defaultValue(0)
                        ->min(0)
                        ->info('time in ms')
                    ->end()
                ->end()
            ->end()
            ->arrayNode('widget_hide_animation')
                ->addDefaultsIfNotSet()
            ->info('animation for hiding collection items')
                ->children()
                    ->enumNode('type')
                        ->values(['hide', 'slide', 'fade'])
                        ->defaultValue('hide')
                    ->end()
                    ->integerNode('length')
                        ->defaultValue(0)
                        ->min(0)
                        ->info('time in ms')
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter('ite_form.component.collection.widget_show_animation', $config['widget_show_animation']);
        $container->setParameter('ite_form.component.collection.widget_hide_animation', $config['widget_hide_animation']);

        parent::loadConfiguration($loader, $config, $container);
    }

    /**
     * {@inheritdoc}
     */
    public function addFormResources(ContainerInterface $container)
    {
        return ['ITEFormBundle:Form/Component/collection:fields.html.twig'];
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        return ['@ITEFormBundle/Resources/public/js/component/Collection/jquery.collection.js'];
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'collection';
    }
}