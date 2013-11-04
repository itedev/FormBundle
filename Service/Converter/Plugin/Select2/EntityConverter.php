<?php

namespace ITE\FormBundle\Service\Converter\Plugin\Select2;

use ITE\FormBundle\Service\Converter\EntityConverter as BaseEntityConverter;
use Symfony\Component\Form\Exception\StringCastException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class EntityConverter
 * @package ITE\FormBundle\Service\Converter\Plugin\Select2
 */
class EntityConverter extends BaseEntityConverter
{
    /**
     * @param $entity
     * @param null $labelPath
     * @return array
     */
    public function convertEntityToOption($entity, $labelPath = null)
    {
        $option = parent::convertEntityToOption($entity, $labelPath);

        return array(
            'id' => $option['id'],
            'text' => $option['label'],
        );
    }

    /**
     * @param $entities
     * @param null $labelPath
     * @return array
     */
    public function convertEntitiesToOptions($entities, $labelPath = null)
    {
        $options = parent::convertEntitiesToOptions($entities, $labelPath);

        return array_map(function($option) {
            return array(
                'id' => $option['id'],
                'text' => $option['label'],
            );
        }, $options);
    }
}