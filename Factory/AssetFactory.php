<?php

namespace ITE\FormBundle\Factory;

use Assetic\Asset\AssetCollection;
use ITE\FormBundle\Service\SFFormExtension;
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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param KernelInterface $kernel
     * @param ContainerInterface $container
     * @param ParameterBagInterface $parameterBag
     * @param string $baseDir
     * @param bool $debug
     */
    public function __construct(KernelInterface $kernel, ContainerInterface $container, ParameterBagInterface $parameterBag, $baseDir, $debug = false)
    {
        parent::__construct($kernel, $container, $parameterBag, $baseDir, $debug);

        $this->container = $container;
    }

    /**
     * @param array $inputs
     * @param array $filters
     * @param array $options
     * @return AssetCollection
     */
    public function createAsset($inputs = array(), $filters = array(), array $options = array())
    {
        if ('.js' === substr($options['output'], -3)) {
            // add plugin js
            foreach (SFFormExtension::getPlugins() as $plugin) {
                if ($enabled = $this->container->getParameter(sprintf('ite_form.plugins.%s.enabled', $plugin))) {
                    $inputs[] = sprintf('@ITEFormBundle/Resources/public/js/plugins/sf.%s.js', $plugin);
                }
            }
        }
        return parent::createAsset($inputs, $filters, $options);
    }

}