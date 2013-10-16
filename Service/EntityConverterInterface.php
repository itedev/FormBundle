<?php

namespace ITE\FormBundle\Service;

/**
 * Class EntityConverterInterface
 * @package ITE\FormBundle\Service
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
}