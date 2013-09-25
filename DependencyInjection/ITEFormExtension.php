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
                $method = 'load' . ucfirst($plugin) . 'Configuration';

                $this->$method($loader, $config['plugins'][$plugin], $container);
            }
        }
    }

    /**
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function loadSelect2Configuration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter('ite_form.plugins.select2.extras', $config['extras']);
        $container->setParameter('ite_form.plugins.select2.options', $config['options']);

        $loader->load('plugins/select2.yml');

        $this->addExtendedChoiceTypes('ite_form.form.type.select2_abstract', 'select2', $container);
    }

    /**
     * @param FileLoader $loader
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function loadTinymceConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter('ite_form.plugins.tinymce.extras', $config['extras']);
        $container->setParameter('ite_form.plugins.tinymce.options', $config['options']);

        $loader->load('plugins/tinymce.yml');
    }

    /**
     * @param $serviceId
     * @param $name
     * @param ContainerBuilder $container
     */
    private function addExtendedChoiceTypes($serviceId, $name, ContainerBuilder $container)
    {
        foreach ($this->getChoiceTypeNames() as $type) {
            $definition = new DefinitionDecorator($serviceId);
            $definition
                ->addMethodCall('setType', array($type))
                ->addTag('form.type', array(
                    'alias' => 'ite_' . $name . '_' . $type)
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
        );
    }
}
