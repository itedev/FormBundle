<?php

namespace ITE\FormBundle\EntityConverter;

/**
 * Class ConverterManager
 * @package ITE\FormBundle\EntityConverter
 */
class ConverterManager implements ConverterManagerInterface
{
    /**
     * @var array|ConverterInterface[] $converters
     */
    protected $converters = [];

    /**
     * @param string $alias
     * @param ConverterInterface $converter
     */
    public function addConverter($alias, ConverterInterface $converter)
    {
        $this->converters[$alias] = $converter;
    }

    /**
     * @param string $alias
     * @return bool
     */
    public function hasConverter($alias)
    {
        return array_key_exists($alias, $this->converters);
    }

    /**
     * @param string $alias
     * @return ConverterInterface
     */
    public function getConverter($alias)
    {
        if (!$this->hasConverter($alias)) {
            throw new \InvalidArgumentException(sprintf('There are no converter with alias "%s"', $alias));
        }

        return $this->converters[$alias];
    }
}