<?php

namespace ITE\FormBundle\SF;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class Plugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class Plugin implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode, ContainerBuilder $container)
    {
        /** @var $node NodeBuilder */
        $node = $rootNode
            ->children()
                ->arrayNode(static::getName())
                    ->canBeUnset()
                    ->canBeEnabled()
        ;

        return $node
            ->children()
                ->variableNode('options')
                    ->defaultValue([])
                ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('ite_form.plugin.%s.options', static::getName()), $config['options']);
        $loader->load(sprintf('plugin/%s.yml', static::getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function addFormResources(ContainerInterface $container)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function addRoutes(Loader $loader, ContainerInterface $container)
    {
        $bundlePath = $container->get('kernel')->getBundle('ITEFormBundle')->getPath();
        $pluginName = Inflector::classify(static::getName());

        $routeCollection = new RouteCollection();
        if (file_exists(sprintf('%s/Controller/Plugin/%s', $bundlePath, $pluginName))) {
            $routeCollection->addCollection($loader->import(
                sprintf('@ITEFormBundle/Controller/Plugin/%s/', $pluginName),
                'annotation'
            ));
        }

        return $routeCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getStylesheets()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        return [sprintf('@ITEFormBundle/Resources/public/js/plugin/%s.js', static::getName())];
    }
}
