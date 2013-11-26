<?php

namespace ITE\FormBundle\Service\Converter;

/**
 * Interface EntityConverterInterface
 * @package ITE\FormBundle\Service\Converter
 */
interface EntityConverterInterface
{
    /**
     * @param $entity
     * @param null $labelPath
     * @return mixed
     */
    public function convertEntityToOption($entity, $labelPath = null);

    /**
     * @param $entities
     * @param null $labelPath
     * @return array
     */
    public function convertEntitiesToOptions($entities, $labelPath = null);

    /**
     * @param $entity
     * @param null $labelPath
     * @return array
     */
    public function convertEntityToChoice($entity, $labelPath = null);

    /**
     * @param $entities
     * @param null $labelPath
     * @return array
     */
    public function convertEntitiesToChoices($entities, $labelPath = null);
}