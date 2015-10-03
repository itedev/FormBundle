<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\Common\CdnJs\CdnAssetReference;
use ITE\FormBundle\SF\AbstractPlugin;

/**
 * Class InputmaskPlugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class InputmaskPlugin extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    public function getCdnName()
    {
        return 'jquery.inputmask';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCdnVersion()
    {
        return '3.1.62';
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnStylesheets($debug)
    {
        return [];
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
                $debug ? 'jquery.inputmask.bundle.js' : 'jquery.inputmask.bundle.min.js'
            ),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'inputmask';
    }
}