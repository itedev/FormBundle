<?php

namespace ITE\FormBundle\SF;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * Interface ExtensionInterface
 * @package ITE\FormBundle\SF
 */
interface ExtensionInterface
{
    const NAME = 'undefined';

    /**
     * @param ArrayNodeDefinition $pluginsNode
     * @param ContainerBuilder $container
     * @return NodeBuilder
     */
    public function addConfiguration(ArrayNodeDefinition $pluginsNode, ContainerBuilder $container);

    /**
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container);

    /**
     * @param ContainerInterface $container
     * @return array
     */
    public function addFormResources(ContainerInterface $container);

    /**
     * @param Loader $loader
     * @param ContainerInterface $container
     * @return RouteCollection
     */
    public function addRoutes(Loader $loader, ContainerInterface $container);

    /**
     * @return array
     */
    public function getStylesheets();

    /**
     * @return array
     */
    public function getJavascripts();

    /**
     * @param ContainerInterface $container
     * @return bool
     */
    public function isEnabled(ContainerInterface $container);
} 