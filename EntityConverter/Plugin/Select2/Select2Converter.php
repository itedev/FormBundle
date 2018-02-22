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
    public function convert($entities, array $options = [])
    {
        $choices = parent::convert($entities, $options);

        if (false === $options['multiple']) {
            $choices = [$choices];
        }

        $convertedChoices = [];
        foreach ($choices as $group => $groupChoices) {
            if (is_array($groupChoices) && !isset($groupChoices['value'])) {
                $children = [];
                foreach ($groupChoices as $choice) {
                    if (isset($choice['options'])) {
                        $children[] = [
                            'id'      => $choice['value'],
                            'text'    => $choice['label'],
                            'options' => $choice['options'],
                        ];
                    } else {
                        $children[] = [
                            'id'   => $choice['value'],
                            'text' => $choice['label'],
                        ];
                    }
                }

                $convertedChoices[] = [
                    'text' => $group,
                    'children' => $children,
                ];
            } else {
                if (isset($groupChoices['options'])) {
                    $convertedChoices[] = [
                        'id'      => $groupChoices['value'],
                        'text'    => $groupChoices['label'],
                        'options' => $groupChoices['options'],
                    ];
                } else {
                    $convertedChoices[] = [
                        'id'   => $groupChoices['value'],
                        'text' => $groupChoices['label'],
                    ];
                }
            }
        }

        return $options['multiple'] ? $convertedChoices : array_pop($convertedChoices);
    }

}