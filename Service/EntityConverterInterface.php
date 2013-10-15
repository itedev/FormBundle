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
     * @param string|null $labelPath
     * @param string|null $idPath
     * @return mixed
     */
    public function convertEntityToOption($entity, $labelPath = null, $idPath = null);

    /**
     * @param $entities
     * @param null $labelPath
     * @return array
     */
    public function convertEntitiesToOptions($entities, $labelPath = null);
}