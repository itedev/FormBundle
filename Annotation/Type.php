<?php

namespace ITE\FormBundle\Annotation;

use ITE\FormBundle\Exception\InvalidArgumentException;
use ITE\FormBundle\Exception\UnexpectedTypeException;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Type
{
    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var array $options
     */
    protected $options = [];

    /**
     * @var callable|null
     */
    protected $optionsModifier;

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
        if (isset($values['optionsModifier'])) {
            if (!is_callable($values['optionsModifier'])) {
                throw new UnexpectedTypeException($values['optionsModifier'], 'callable');
            }
            $this->optionsModifier = $values['optionsModifier'];
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

    /**
     * Get optionsModifier
     *
     * @return callable|null
     */
    public function getOptionsModifier()
    {
        return $this->optionsModifier;
    }
}
