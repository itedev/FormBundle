<?php

namespace ITE\FormBundle\EntityConverter;

/**
 * Interface ConverterInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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