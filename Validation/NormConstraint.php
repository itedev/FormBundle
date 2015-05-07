<?php

namespace ITE\FormBundle\Validation;

/**
 * Class NormConstraint
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class NormConstraint implements NormConstraintInterface
{
    /**
     * @var string $type
     */
    private $type;

    /**
     * @var string $message
     */
    private $message;

    /**
     * @var array $options
     */
    private $options = [];

    /**
     * @var array $attributes
     */
    private $attributes = [];

    /**
     * @param string $type
     * @param string $message
     * @param array $options
     */
    public function __construct($type, $message, array $options = [])
    {
        $this->type = $type;
        $this->message = $message;
        $this->options = $options;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
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
     * @param string $name
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public function getOption($name, $defaultValue = null)
    {
        return array_key_exists($name, $this->options) ? $this->options[$name] : $defaultValue;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     * @return NormConstraint
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param null $defaultValue
     * @return null
     */
    public function getAttribute($name, $defaultValue = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $defaultValue;
    }
}