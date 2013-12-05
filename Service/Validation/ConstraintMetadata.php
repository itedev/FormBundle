<?php

namespace ITE\FormBundle\Service\Validation;

/**
 * Class ConstraintMetadata
 * @package ITE\FormBundle\Service\Validation
 */
class ConstraintMetadata implements ConstraintMetadataInterface
{
    /**
     * @var string $type
     */
    protected $type;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var array $options
     */
    protected $options = array();

    /**
     * @param $type
     * @param $message
     * @param array $options
     */
    public function __construct($type, $message, $options = array())
    {
        $this->type = $type;
        $this->message = $message;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

}
