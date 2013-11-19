<?php

namespace ITE\FormBundle\SF;

/**
 * Class Element
 * @package ITE\FormBundle\SF
 */
class Element
{
    /**
     * @var string $selector
     */
    protected $selector;

    /**
     * @var array $parents
     */
    protected $parents = array();

    /**
     * @var array $options
     */
    protected $options = array();

    /**
     * @param $selector
     * @param array $parents
     * @param array $options
     */
    public function __construct($selector, $parents = array(), $options = array())
    {
        $this->selector = $selector;
        $this->parents = $parents;
        $this->options = $options;
    }

    /**
     * Get parents
     *
     * @return array
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * Get selector
     *
     * @return string
     */
    public function getSelector()
    {
        return $this->selector;
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