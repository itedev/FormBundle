<?php

namespace ITE\FormBundle\EntityConverter;

/**
 * Interface ConverterManagerInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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