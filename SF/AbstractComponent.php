<?php

namespace ITE\FormBundle\SF;

use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AbstractComponent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractComponent implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ContainerBuilder $container)
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root(static::getName());
        $rootNode
            ->canBeUnset()
            ->canBeEnabled()
        ;

        return $rootNode;
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
        return [];
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
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        return [];
    }
}
