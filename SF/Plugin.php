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
 * @package ITE\FormBundle\SF
 */
class Plugin implements ExtensionInterface
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
            ->booleanNode('enabled')->defaultFalse()->end()
            ->variableNode('options')->defaultValue(array())->end()
        ;

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('ite_form.plugin.%s.options', static::NAME), $config['options']);
        $loader->load(sprintf('plugin/%s.yml', static::NAME));
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
        $pluginName = Inflector::classify(static::NAME);

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
    public function addStylesheets(ContainerInterface $container)
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function addJavascripts(ContainerInterface $container)
    {
        return array(sprintf('@ITEFormBundle/Resources/public/js/plugin/%s.js', static::NAME));
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(ContainerInterface $container)
    {
        return $container->getParameter(sprintf('ite_form.plugin.%s.enabled', static::NAME));
    }
} 