<?php

namespace ITE\FormBundle\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Type
{
    /**
     * @var string|null $type
     */
    protected $type = null;

    /**
     * @var array $options
     */
    protected $options = array();

    /**
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['type'])) {
            $this->type = $values['type'];
        }
        if (isset($values['options'])) {
            $this->options = $values['options'];
        }
    }

    /**
     * Get type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}