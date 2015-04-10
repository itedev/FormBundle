<?php

namespace ITE\FormBundle\EntityConverter\Plugin\Select2;

use ITE\FormBundle\EntityConverter\DefaultConverter;

/**
 * Class Select2Converter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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