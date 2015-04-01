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
 * Class Component
 * @package ITE\FormBundle\SF
 */
abstract class Component implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode, ContainerBuilder $container)
    {
        /** @var $node NodeBuilder */
        return $rootNode
            ->children()
                ->arrayNode(static::getName())
                    ->canBeUnset()
                    ->canBeEnabled()
                    ->children()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $loader->load(sprintf('component/%s.yml', static::getName()));
    }

    /**
     * {@inheritdoc}
     */
    public function addFormResources(ContainerInterface $container)
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function addRoutes(Loader $loader, ContainerInterface $container)
    {
        $bundlePath = $container->get('kernel')->getBundle('ITEFormBundle')->getPath();
        $componentName = Inflector::classify(static::getName());

        $routeCollection = new RouteCollection();
        if (file_exists(sprintf('%s/Controller/Component/%s', $bundlePath, $componentName))) {
            $routeCollection->addCollection($loader->import(
                    sprintf('@ITEFormBundle/Controller/Component/%s/', $componentName),
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
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        return array();
    }

} 