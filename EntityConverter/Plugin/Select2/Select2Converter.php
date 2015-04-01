<?php

namespace ITE\FormBundle\EntityConverter\Plugin\Select2;

use ITE\FormBundle\EntityConverter\DefaultConverter;

/**
 * Class Select2Converter
 * @package ITE\FormBundle\EntityConverter\Plugin\Select2
 */
class Select2Converter extends DefaultConverter
{
    /**
     * {@inheritdoc}
     */
    public function convert($entities, $labelPath = null)
    {
        $options = parent::convert($entities, $labelPath);

        return array_map(function($option) {
            return array(
                'id' => $option['value'],
                'text' => $option['label'],
            );
        }, $options);
    }

}