<?php

namespace ITE\FormBundle\DependencyInjection;

use Doctrine\Common\Inflector\Inflector;
use ITE\FormBundle\SF\SFForm;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ITEFormExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('sf.yml');
        $loader->load('services.yml');

        $container->setParameter('ite_form.timezone', $config['timezone']);

        // load ajax file upload configuration
        if (isset($config['ajax_file_upload'])) {
            $this->loadAjaxFileUploadConfiguration($loader, $config['ajax_file_upload'], $container);
        }

        // load components configuration
        foreach (SFForm::$components as $component) {
            $enabled = isset($config['components'][$component]) && !empty($config['components'][$component]['enabled']);
            $container->setParameter(sprintf('ite_form.component.%s.enabled', $component), $enabled);

            if ($enabled) {
                // load specific component configuration
                $method = 'load' . Inflector::classify($component) . 'ComponentConfiguration';
                if (method_exists($this, $method)) {
                    $this->$method($component, $loader, $config['components'][$component], $container);
                }

                // load common component configuration
                $this->loadComponentConfiguration($component, $loader, $config['components'][$component], $container);
            }
        }

        // load plugins configuration
        foreach (SFForm::$plugins as $plugin) {
            $enabled = isset($config['plugins'][$plugin]) && !empty($config['plugins'][$plugin]['enabled']);
            $container->setParameter(sprintf('ite_form.plugin.%s.enabled', $plugin), $enabled);

            if ($enabled) {
                // load common plugin configuration
                $this->loadPluginConfiguration($plugin, $loader, $config['plugins'][$plugin], $container);

                // load specific plugin configuration
                $method = 'load' . Inflector::classify($plugin) . 'PluginConfiguration';
                if (method_exists($this, $method)) {
                    $this->$method($plugin, $loader, $config['plugins'][$plugin], $container);
                }
            }
        }
    }

    /**
     * @param $component
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function loadComponentConfiguration($component, FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $loader->load(sprintf('component/%s.yml', $component));
    }

    /**
     * @param $component
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function loadAjaxFileUploadComponentConfiguration($component, FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter('ite_form.file_manager.web_root', $config['web_root']);
        $container->setParameter('ite_form.file_manager.tmp_prefix', $config['tmp_prefix']);
    }

    /**
     * @param $plugin
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function loadPluginConfiguration($plugin, FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('ite_form.plugin.%s.options', $plugin), $config['options']);

        $loader->load(sprintf('plugin/%s.yml', $plugin));
    }

    /**
     * @param $plugin
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function loadSelect2PluginConfiguration($plugin, FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $this->addExtendedChoiceTypes(sprintf('ite_form.form.type.plugin.%s.abstract', $plugin), $plugin, $container);
    }

    /**
     * @param $plugin
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function loadFileuploadPluginConfiguration($plugin, FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('ite_form.plugin.%s.%s', $plugin, 'file_manager'), $config['file_manager']);
    }

    /**
     * @param $serviceId
     * @param $plugin
     * @param ContainerBuilder $container
     */
    private function addExtendedChoiceTypes($serviceId, $plugin, ContainerBuilder $container)
    {
        foreach ($this->getChoiceTypeNames() as $type) {
            $definition = new DefinitionDecorator($serviceId);
            $definition
              ->addMethodCall('setType', array($type))
              ->addTag('form.type', array(
                      'alias' => sprintf('ite_%s_%s', $plugin, $type))
              );

            $extendedServiceId = preg_replace('/(abstract)$/', $type, $serviceId);
            $container->setDefinition($extendedServiceId, $definition);
        }
    }

    /**
     * @return array
     */
    private function getChoiceTypeNames()
    {
        return array(
            'choice',
            'language',
            'country',
            'timezone',
            'locale',
            'currency',
            'entity',
            'document',
            'model',
        );
    }
}
