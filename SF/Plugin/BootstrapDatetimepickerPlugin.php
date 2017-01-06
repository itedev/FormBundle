<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\Common\CdnJs\CdnAssetReference;
use ITE\FormBundle\SF\AbstractPlugin;

/**
 * Class BootstrapDatetimepickerPlugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class BootstrapDatetimepickerPlugin extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    public function getCdnName()
    {
        return 'bootstrap-datetimepicker';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCdnVersion()
    {
        return '4.17.37';
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
                'css/bootstrap-datetimepicker.min.css'
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
                'js/bootstrap-datetimepicker.min.js'
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'bootstrap_datetimepicker';
    }
}
