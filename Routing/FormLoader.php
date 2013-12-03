<?php

namespace ITE\FormBundle\Routing;

use Doctrine\Common\Inflector\Inflector;
use ITE\FormBundle\SF\SFForm;
use Symfony\Component\Config\Loader\Loader as BaseLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class FormLoader
 * @package ITE\FormBundle\Routing
 */
class FormLoader extends BaseLoader
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
        $controllerPath = $this->container->get('kernel')->getBundle('ITEFormBundle')->getPath() . '/Controller';

        // load components configuration
        foreach (SFForm::$components as $component) {
            $enabled = $this->container->getParameter(sprintf('ite_form.component.%s.enabled', $component));
            if ($enabled) {
                $componentName = Inflector::classify($component);
                if (file_exists(sprintf('%s/Component/%s', $controllerPath, $componentName))) {
                    $importedRoutes = $this->import(
                        sprintf('@ITEFormBundle/Controller/Component/%s/', $componentName),
                        'annotation'
                    );
                    $collection->addCollection($importedRoutes);
                }
            }
        }

        // load plugins configuration
        foreach (SFForm::$plugins as $plugin) {
            $enabled = $this->container->getParameter(sprintf('ite_form.plugin.%s.enabled', $plugin));
            if ($enabled) {
                $pluginName = Inflector::classify($plugin);
                if (file_exists(sprintf('%s/Plugin/%s', $controllerPath, $pluginName))) {
                    $importedRoutes = $this->import(
                        sprintf('@ITEFormBundle/Controller/Plugin/%s/', $pluginName),
                        'annotation'
                    );
                    $collection->addCollection($importedRoutes);
                }
            }
        }

        return $collection;
    }

    /**
     * @inheritdoc
     */
    public function supports($resource, $type = null)
    {
        return 'ite_form' === $type;
    }
} 