<?php

namespace ITE\FormBundle\Factory;

use Assetic\Asset\AssetCollection;
use Symfony\Bundle\AsseticBundle\Factory\AssetFactory as BaseAssetFactory;

class AssetFactory extends BaseAssetFactory
{

    /**
     * @param array $inputs
     * @param array $filters
     * @param array $options
     * @return AssetCollection
     */
    public function createAsset($inputs = array(), $filters = array(), array $options = array())
    {
        if ('.js' === substr($options['output'], -3)) {
            $inputs = array_merge($inputs, array(
                '@ITEFormBundle/Resources/public/js/plugins/sf.select2.js',
            ));
        }
        return parent::createAsset($inputs, $filters, $options);
    }

}