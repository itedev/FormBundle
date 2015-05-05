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
}