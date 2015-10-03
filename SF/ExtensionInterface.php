<?php

namespace ITE\FormBundle\SF;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * Interface ExtensionInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface ExtensionInterface
{
    /**
     * @param ContainerBuilder $container
     * @return NodeDefinition
     */
    public function addConfiguration(ContainerBuilder $container);

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
     * @return string
     */
    public static function getName();
}
