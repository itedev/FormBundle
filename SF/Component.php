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
class Component implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function addConfiguration(ArrayNodeDefinition $rootNode, ContainerBuilder $container)
    {
        /** @var $node NodeBuilder */
        $node = $rootNode
            ->children()
                ->arrayNode(static::NAME)
                    ->canBeUnset()
                    ->addDefaultsIfNotSet()
                    ->treatFalseLike(array('enabled' => false))
                    ->treatTrueLike(array('enabled' => true))
                    ->treatNullLike(array('enabled' => true))
                    ->children()
        ;

        $node
            ->booleanNode('enabled')->defaultFalse()
        ;

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $loader->load(sprintf('component/%s.yml', static::NAME));
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
        $componentName = Inflector::classify(static::NAME);

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
    public function addStylesheets(ContainerInterface $container)
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function addJavascripts(ContainerInterface $container)
    {
        $bundlePath = $container->get('kernel')->getBundle('ITEFormBundle')->getPath();
        if (file_exists(sprintf('%s/Resources/public/js/component/%s.js', $bundlePath, static::NAME))) {
            return array(sprintf('@ITEFormBundle/Resources/public/js/component/%s.js', static::NAME));
        }

        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(ContainerInterface $container)
    {
        return $container->getParameter(sprintf('ite_form.component.%s.enabled', static::NAME));
    }
} 