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
     * @param mixes $entities
     * @param array $options
     *
     * @return mixed
     */
    public function convert($entities, array $options = []);
}