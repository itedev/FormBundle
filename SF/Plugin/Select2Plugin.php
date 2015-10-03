<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\Common\CdnJs\CdnAssetReference;
use ITE\FormBundle\SF\ChoicePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class Select2Plugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Select2Plugin extends ChoicePlugin
{
    /**
     * {@inheritdoc}
     */
    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        parent::loadConfiguration($loader, $config, $container);

        $this->addExtendedChoiceTypes(sprintf('ite_form.form.type.plugin.%s.abstract', static::getName()), static::getName(), $container);
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnName()
    {
        return 'select2';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCdnVersion()
    {
        return '4.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnStylesheets($debug)
    {
        return [
            new CdnAssetReference(
                $this->getCdnName(),
                $this->getCdnVersion(),
                $debug ? 'css/select2.css' : 'css/select2.min.css'
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnJavascripts($debug)
    {
        return [
            new CdnAssetReference(
                $this->getCdnName(),
                $this->getCdnVersion(),
                $debug ? 'js/select2.js' : 'js/select2.min.js'
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'select2';
    }
}
