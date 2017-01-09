<?php

namespace ITE\FormBundle\DependencyInjection;

use ITE\FormBundle\SF\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class ITEFormExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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

        $loader->load('overridden.yml');
        $loader->load('proxy.yml');
        $loader->load('sf.yml');
        $loader->load('types.yml');
        $loader->load('hidden.yml');
        $loader->load('range.yml');
        $loader->load('type_extensions.yml');
        $loader->load('type_guessers.yml');
        $loader->load('entity_converters.yml');

        $container->setParameter('ite_form.timezone', $config['timezone']);

        $this->loadComponentsConfiguration($loader, $config['components'], $container);
        $this->loadPluginsConfiguration($loader, $config['plugins'], $container);
        $this->loadTypesConfiguration($loader, $config, $container);
        $this->loadTypeExtensionsConfiguration($loader, $config, $container);
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

            $enabled = isset($config[$component::getName()]) && !empty($config[$component::getName()]['enabled']);
            $container->setParameter(sprintf('ite_form.component.%s.enabled', $component::getName()), $enabled);

            if ($enabled) {
                $component->loadConfiguration($loader, $config[$component::getName()], $container);
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

            $enabled = isset($config[$plugin::getName()]) && !empty($config[$plugin::getName()]['enabled']);
            $container->setParameter(sprintf('ite_form.plugin.%s.enabled', $plugin::getName()), $enabled);

            if ($enabled) {
                $plugin->loadConfiguration($loader, $config[$plugin::getName()], $container);
            }
        }
    }

    /**
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function loadTypesConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        foreach ($config['types'] as $type => $typeConfiguration) {
            $typeDefinitionDecorator = new DefinitionDecorator('ite_form.form.type.dynamic.abstract');
            $typeServiceId = sprintf('ite_form.form.type.%s', $type);

            $container
                ->setDefinition($typeServiceId, $typeDefinitionDecorator)
                ->replaceArgument(0, $type)
                ->replaceArgument(1, $typeConfiguration['parent'])
                ->replaceArgument(2, $typeConfiguration['options'])
                ->addTag('form.type', [
                    'alias' => $type,
                ])
            ;
        }
    }

    /**
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function loadTypeExtensionsConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        foreach ($config['type_extensions'] as $type => $options) {
            if (empty($options)) {
                continue;
            }

            $typeExtensionDefinitionDecorator = new DefinitionDecorator('ite_form.form.type_extension.dynamic.default_configuration.abstract');
            $typeExtensionServiceId = sprintf('ite_form.form.type_extension.%s.default_configuration', $type);

            $container
                ->setDefinition($typeExtensionServiceId, $typeExtensionDefinitionDecorator)
                ->replaceArgument(0, $type)
                ->replaceArgument(1, $options)
                ->addTag('form.type_extension', [
                    'alias' => $type,
                ])
            ;
        }
    }
}
