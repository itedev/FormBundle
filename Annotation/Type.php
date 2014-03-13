<?php

namespace ITE\FormBundle\Annotation;
use InvalidArgumentException;

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
     * @throws InvalidArgumentException
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->type = $values['value'];
        } elseif (isset($values['type'])) {
            $this->type = $values['type'];
        } else {
            throw new InvalidArgumentException(sprintf('"type" must be defined for "%s" annotation.', get_class($this)));
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