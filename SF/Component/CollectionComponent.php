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
 * @package ITE\FormBundle\SF\Component
 */
class CollectionComponent extends Component
{
    const NAME = 'collection';

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode, ContainerBuilder $container)
    {
        /** @var $node NodeBuilder */
        $node = parent::addConfiguration($rootNode, $container);
        $node
            ->enumNode('type')
                ->values(array('bs2', 'bs3'))
                ->isRequired()
            ->end()
            ->arrayNode('widget_show_animation')
                ->addDefaultsIfNotSet()
                ->info('animation for showing new collection items')
                ->children()
                    ->enumNode('type')
                        ->defaultValue('show')
                        ->values(array('show', 'slide', 'fade'))
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
                        ->values(array('hide', 'slide', 'fade'))
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
        $container->setParameter('ite_form.component.collection.type', $config['type']);
        $container->setParameter('ite_form.component.collection.widget_show_animation', $config['widget_show_animation']);
        $container->setParameter('ite_form.component.collection.widget_hide_animation', $config['widget_hide_animation']);

        parent::loadConfiguration($loader, $config, $container);
    }

    /**
     * {@inheritdoc}
     */
    public function addFormResources(ContainerInterface $container)
    {
        switch ($container->getParameter('ite_form.component.collection.type')) {
            case 'bs2':
                $template = 'ITEFormBundle:Form/Component/collection:bs2.html.twig';
                break;
            case 'bs3':
                $template = 'ITEFormBundle:Form/Component/collection:bs3.html.twig';
                break;
        }

        return array($template);
    }

}