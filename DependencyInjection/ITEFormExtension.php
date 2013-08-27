<?php

namespace ITE\FormBundle\DependencyInjection;

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
     * @var array $plugins
     */
    protected $plugins = array(
        'tinymce'
    );

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

        foreach ($this->plugins as $plugin) {
            if (isset($config['plugins'][$plugin]) && !empty($config['plugins'][$plugin]['enabled'])) {
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
    protected function loadTinymceConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        $container->setParameter('ite_form.plugins.tinymce.extras', $config['extras']);
        $container->setParameter('ite_form.plugins.tinymce.options', $config['options']);

        $loader->load('tinymce.yml');
    }
}
