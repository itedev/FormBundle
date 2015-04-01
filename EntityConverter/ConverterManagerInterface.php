<?php

namespace ITE\FormBundle\EntityConverter;

/**
 * Interface ConverterManagerInterface
 * @package ITE\FormBundle\EntityConverter
 */
interface ConverterManagerInterface
{
    /**
     * @param string $alias
     * @param ConverterInterface $converter
     */
    public function addConverter($alias, ConverterInterface $converter);

    /**
     * @param string $alias
     * @return bool
     */
    public function hasConverter($alias);

    /**
     * @param string $alias
     * @return ConverterInterface
     */
    public function getConverter($alias);


}