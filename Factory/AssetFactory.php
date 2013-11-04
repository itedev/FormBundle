<?php

namespace ITE\FormBundle\Factory;

use Assetic\Asset\AssetCollection;
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
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritdoc}
     */
    public function __construct(KernelInterface $kernel, ContainerInterface $container, ParameterBagInterface $parameterBag, $baseDir, $debug = false)
    {
        parent::__construct($kernel, $container, $parameterBag, $baseDir, $debug);

        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function createAsset($inputs = array(), $filters = array(), array $options = array())
    {
        if ('.js' === substr($options['output'], -3)) {
            // add plugin js
            foreach (SFFormExtension::getPlugins() as $plugin) {
                $enabled = $this->container->getParameter(sprintf('ite_form.plugin.%s.enabled', $plugin));
                if ($enabled) {
                    $inputs[] = sprintf('@ITEFormBundle/Resources/public/js/plugin/sf.%s.js', $plugin);
                }
            }
        }
        return parent::createAsset($inputs, $filters, $options);
    }

}