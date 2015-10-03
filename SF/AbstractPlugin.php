<?php

namespace ITE\FormBundle\SF;

use Doctrine\Common\Inflector\Inflector;
use ITE\Common\CdnJs\CdnAssetReference;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AbstractPlugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractPlugin implements ExtensionInterface
{
    /**
     * @var array
     */
    protected $cdn;

    /**
     * @param array $cdn
     */
    public function setCdn(array $cdn)
    {
        $this->cdn = $cdn;
    }

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
            ->children()
                ->variableNode('options')
                    ->defaultValue([])
                ->end()
            ->end()
        ;

        if ($this->hasCdn()) {
            $rootNode
                ->children()
                    ->arrayNode('cdn')
                        ->canBeUnset()
                        ->canBeDisabled()
                        ->children()
                            ->scalarNode('version')
                                ->defaultValue($this->getDefaultCdnVersion())
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ;
        }

        return $rootNode;
    }

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('ite_form.plugin.%s.options', static::getName()), $config['options']);
        if ($this->hasCdn()) {
            $container->setParameter(sprintf('ite_form.plugin.%s.cdn', static::getName()), $config['cdn']);
        }
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

    /**
     * @return bool
     */
    public function hasCdn()
    {
        return null !== $this->getCdnName();
    }

    /**
     * @return bool
     */
    public function isCdnEnabled()
    {
        return $this->cdn && $this->cdn['enabled'];
    }

    /**
     * @return string|null
     */
    public function getCdnVersion()
    {
        return $this->cdn ? $this->cdn['version'] : $this->getDefaultCdnVersion();
    }

    /**
     * @return string|null
     */
    public function getCdnName()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getDefaultCdnVersion()
    {
        return null;
    }

    /**
     * @param bool $debug
     * @return array|CdnAssetReference[]
     */
    public function getCdnStylesheets($debug)
    {
        return [];
    }

    /**
     * @param bool $debug
     * @return array|CdnAssetReference[]
     */
    public function getCdnJavascripts($debug)
    {
        return [];
    }
}
