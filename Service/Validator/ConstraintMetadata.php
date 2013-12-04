<?php

namespace ITE\FormBundle\Service\Validator;

/**
 * Class ConstraintMetadata
 * @package ITE\FormBundle\Service\Validator
 */
class ConstraintMetadata
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
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

}
