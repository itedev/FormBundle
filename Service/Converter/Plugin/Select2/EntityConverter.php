<?php

namespace ITE\FormBundle\Service\Converter\Plugin\Select2;

use ITE\FormBundle\Service\Converter\EntityConverter as BaseEntityConverter;

/**
 * Class EntityConverter
 * @package ITE\FormBundle\Service\Converter\Plugin\Select2
 */
class EntityConverter extends BaseEntityConverter implements EntityConverterInterface
{
    /**
     * @param object $entity
     * @param null $labelPath
     * @return array
     */
    public function convertEntityToOption($entity, $labelPath = null)
    {
        $option = parent::convertEntityToOption($entity, $labelPath);

        return array(
            'id' => $option['value'],
            'text' => $option['label'],
        );
    }

    /**
     * @param array $entities
     * @param null $labelPath
     * @return array
     */
    public function convertEntitiesToOptions($entities, $labelPath = null)
    {
        $options = parent::convertEntitiesToOptions($entities, $labelPath);

        return array_map(function($option) {
            return array(
                'id' => $option['value'],
                'text' => $option['label'],
            );
        }, $options);
    }

    /**
     * @param array $choices
     * @return array
     */
    public function convertChoicesToOptions($choices)
    {
        return array_map(array($this, 'convertChoiceToOption'), array_values($choices));
    }

    /**
     * @param object $choice
     * @return array
     */
    public function convertChoiceToOption($choice)
    {
        return array(
            'id' => $choice->value,
            'text' => $choice->label
        );
    }
}