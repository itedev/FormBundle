<?php

namespace ITE\FormBundle\Service\Converter;

/**
 * Class ChoiceConverter
 * @package ITE\FormBundle\Service\Converter
 */
class ChoiceConverter
{
    /**
     * @param array $options
     * @return array
     */
    public function convertOptionsToChoices(array $options)
    {
        $choices = array();
        foreach ($options as $option) {
            $choices[$option['value']] = $option['label'];
        }

        return $choices;
    }

    /**
     * @param array $choices
     * @return array
     */
    public function convertChoicesToOptions(array $choices)
    {
        $options = array();
        foreach ($choices as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label,
            );
        }

        return $options;
    }
} 