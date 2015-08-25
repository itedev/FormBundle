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

        $convertedChoices = [];
        foreach ($choices as $group => $groupChoices) {
            if (is_array($groupChoices) && !isset($groupChoices['value'])) {
                $children = [];
                foreach ($groupChoices as $choice) {
                    $children[] = [
                        'id' => $choice['value'],
                        'text' => $choice['label'],
                    ];
                }

                $convertedChoices[] = [
                    'text' => $group,
                    'children' => $children,
                ];
            } else {
                $convertedChoices[] = [
                    'id' => $groupChoices['value'],
                    'text' => $groupChoices['label'],
                ];
            }
        }

        return $convertedChoices;
    }

}