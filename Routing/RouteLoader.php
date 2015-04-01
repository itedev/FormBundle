<?php

namespace ITE\FormBundle\Routing;

use Doctrine\Common\Inflector\Inflector;
use ITE\FormBundle\SF\ExtensionInterface;
use ITE\FormBundle\SF\SFForm;
use ITE\FormBundle\SF\SFFormExtensionInterface;
use Symfony\Component\Config\Loader\Loader as BaseLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteLoader
 * @package ITE\FormBundle\Routing
 */
class RouteLoader extends BaseLoader
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritdoc
     */
    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();

        $sfForm = $this->container->get('ite_form.sf.extension.form');
        $this->addComponentsRoutes($sfForm, $collection, $this->container);
        $this->addPluginsRoutes($sfForm, $collection, $this->container);

        return $collection;
    }

    /**
     * @param SFFormExtensionInterface $sfForm
     * @param RouteCollection $collection
     * @param ContainerInterface $container
     */
    protected function addComponentsRoutes(SFFormExtensionInterface $sfForm, RouteCollection $collection, ContainerInterface $container)
    {
        foreach ($sfForm->getComponents() as $component) {
            /** @var $component ExtensionInterface */
            $collection->addCollection($component->addRoutes($this, $container));
        }
    }

    /**
     * @param SFFormExtensionInterface $sfForm
     * @param RouteCollection $collection
     * @param ContainerInterface $container
     */
    protected function addPluginsRoutes(SFFormExtensionInterface $sfForm, RouteCollection $collection, ContainerInterface $container)
    {
        foreach ($sfForm->getPlugins() as $plugin) {
            /** @var $plugin ExtensionInterface */
            $collection->addCollection($plugin->addRoutes($this, $container));
        }
    }

    /**
     * @inheritdoc
     */
    public function supports($resource, $type = null)
    {
        return 'ite_form' === $type;
    }
} 