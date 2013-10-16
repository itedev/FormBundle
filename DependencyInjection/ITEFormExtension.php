<?php

namespace ITE\FormBundle\DependencyInjection;

use ITE\FormBundle\Service\SFFormExtension;
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
        $loader->load('services.yml');

        $container->setParameter('ite_form.timezone', $config['timezone']);

        // load plugins configuration
        foreach (SFFormExtension::getPlugins() as $plugin) {
            $enabled = isset($config['plugins'][$plugin]) && !empty($config['plugins'][$plugin]['enabled']);
            $container->setParameter(sprintf('ite_form.plugins.%s.enabled', $plugin), $enabled);

            if ($enabled) {
                // load common plugin configuration
                $this->loadPluginConfiguration($plugin, $loader, $config['plugins'][$plugin], $container);

                // load specific plugin configuration
                $method = 'load' . ucfirst($plugin) . 'Configuration';
                if (method_exists($this, $method)) {
                    $this->$method($loader, $config['plugins'][$plugin], $container);
                }
            }
        }
    }

    /**
     * @param $plugin
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function loadPluginConfiguration($plugin, FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter(sprintf('ite_form.plugins.%s.extras', $plugin), $config['extras']);
        $container->setParameter(sprintf('ite_form.plugins.%s.options', $plugin), $config['options']);

        $loader->load(sprintf('plugins/%s.yml', $plugin));
    }

    /**
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function loadSelect2Configuration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $this->addExtendedChoiceTypes('ite_form.form.type.select2_abstract', 'select2', $container);
    }

    /**
     * @param $serviceId
     * @param $plugin
     * @param ContainerBuilder $container
     */
    protected function addExtendedChoiceTypes($serviceId, $plugin, ContainerBuilder $container)
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
    protected function getChoiceTypeNames()
    {
        return array(
            'choice',
            'language',
            'country',
            'timezone',
            'locale',
            'currency',
            'entity',
        );
    }
}
