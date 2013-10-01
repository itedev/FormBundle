<?php

namespace ITE\FormBundle\Service\Plugin;

use ITE\FormBundle\Service\EntityConverter;
use Symfony\Component\Form\Exception\StringCastException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Select2Converter
{
    /**
     * @var EntityConverter
     */
    protected $entityConverter;

    /**
     * @param EntityConverter $entityConverter
     */
    public function __construct(EntityConverter $entityConverter)
    {
        $this->entityConverter = $entityConverter;
    }

    /**
     * @param $entity
     * @param null $labelPath
     * @return array
     */
    public function convertEntityToOption($entity, $labelPath = null)
    {
        $option = $this->entityConverter->convertEntityToOption($entity, $labelPath);

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
        $options = $this->entityConverter->convertEntitiesToOptions($entities, $labelPath);

        return array_map(function($option) {
                return array(
                    'id' => $option['id'],
                    'text' => $option['label'],
                );
            }, $options);
    }
}