<?php

namespace ITE\FormBundle\DependencyInjection;

use ITE\FormBundle\SF\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
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
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('components.yml');
        $loader->load('plugins.yml');

        $configuration = new Configuration($container);
        $config = $this->processConfiguration($configuration, $configs);

        $loader->load('sf.yml');
        $loader->load('services.yml');

        $container->setParameter('ite_form.timezone', $config['timezone']);

        $this->loadComponentsConfiguration($loader, $config['components'], $container);
        $this->loadPluginsConfiguration($loader, $config['plugins'], $container);
    }

    /**
     * {@inheritDoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration($container);
    }

    /**
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function loadComponentsConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('ite_form.component');
        foreach ($serviceIds as $serviceId => $attributes) {
            /** @var $component ExtensionInterface */
            $component = $container->get($serviceId);

            $enabled = isset($config[$component::NAME]) && !empty($config[$component::NAME]['enabled']);
            $container->setParameter(sprintf('ite_form.component.%s.enabled', $component::NAME), $enabled);

            if ($enabled) {
                $component->loadConfiguration($loader, $config[$component::NAME], $container);
            }
        }
    }

    /**
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function loadPluginsConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds('ite_form.plugin');
        foreach ($serviceIds as $serviceId => $attributes) {
            /** @var $plugin ExtensionInterface */
            $plugin = $container->get($serviceId);

            $enabled = isset($config[$plugin::NAME]) && !empty($config[$plugin::NAME]['enabled']);
            $container->setParameter(sprintf('ite_form.plugin.%s.enabled', $plugin::NAME), $enabled);

            if ($enabled) {
                $plugin->loadConfiguration($loader, $config[$plugin::NAME], $container);
            }
        }
    }
}
