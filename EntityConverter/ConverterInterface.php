<?php

namespace ITE\FormBundle\EntityConverter;

/**
 * Interface ConverterInterface
 * @package ITE\FormBundle\EntityConverter
 */
interface ConverterInterface
{
    /**
     * @param array $entities
     * @param null $labelPath
     * @return array
     */
    public function convert($entities, $labelPath = null);
}