<?php

namespace ITE\FormBundle\Factory;

use Assetic\Asset\AssetCollection;
use ITE\FormBundle\Components;
use ITE\FormBundle\SF\SFFormExtension;
use Symfony\Bundle\AsseticBundle\Factory\AssetFactory as BaseAssetFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AssetFactory
 * @package ITE\FormBundle\Factory
 */
class AssetFactory extends BaseAssetFactory
{
    /**
     * @var KernelInterface $kernel
     */
    protected $kernel;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function __construct(KernelInterface $kernel, ContainerInterface $container, ParameterBagInterface $parameterBag, $baseDir, $debug = false)
    {
        parent::__construct($kernel, $container, $parameterBag, $baseDir, $debug);

        $this->kernel = $kernel;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function createAsset($inputs = array(), $filters = array(), array $options = array())
    {
        if ('.js' === substr($options['output'], -3)) {
            $bundlePath = $this->kernel->getBundle('ITEFormBundle')->getPath();

            // add component js
            foreach (Components::$components as $component) {
                $enabled = $this->container->getParameter(sprintf('ite_form.component.%s.enabled', $component));
                if ($enabled && file_exists(sprintf('%s/Resources/public/js/component/%s.js',
                        $bundlePath,  $component))) {
                    $inputs[] = sprintf('@ITEFormBundle/Resources/public/js/component/%s.js', $component);
                }
            }

            // add plugin js
            foreach (SFFormExtension::getPlugins() as $plugin) {
                $enabled = $this->container->getParameter(sprintf('ite_form.plugin.%s.enabled', $plugin));
                if ($enabled) {
                    $inputs[] = sprintf('@ITEFormBundle/Resources/public/js/plugin/%s.js', $plugin);
                }
            }
        }
        return parent::createAsset($inputs, $filters, $options);
    }

}