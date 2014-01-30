<?php

namespace ITE\FormBundle\Service\Converter;

/**
 * Class ChoiceConverter
 * @package ITE\FormBundle\Service\Converter
 */
class ChoiceConverter
{
    /**
     * @param array $choices
     * @return array
     */
    public function convertChoicesToOptions(array $choices)
    {
        $options = array();
        foreach ($choices as $choice) {
            $options[$choice['value']] = $choice['label'];
        }

        return $options;
    }

    /**
     * @param array $options
     * @return array
     */
    public function convertOptionsToChoices(array $options)
    {
        $choices = array();
        foreach ($options as $value => $label) {
            $choices[] = array(
                'value' => $value,
                'label' => $label,
            );
        }

        return $choices;
    }
} 