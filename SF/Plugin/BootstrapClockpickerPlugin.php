<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\Common\CdnJs\CdnAssetReference;
use ITE\FormBundle\SF\AbstractPlugin;

/**
 * Class BootstrapClockpickerPlugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class BootstrapClockpickerPlugin extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    public function getCdnName()
    {
        return 'clockpicker';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCdnVersion()
    {
        return '0.0.7';
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
                $debug ? 'bootstrap-clockpicker.css' : 'bootstrap-clockpicker.min.css'
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
                $debug ? 'bootstrap-clockpicker.js' : 'bootstrap-clockpicker.min.js'
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'bootstrap_clockpicker';
    }
}
