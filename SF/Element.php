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
     * @var array $options
     */
    protected $options = array();

    /**
     * @param $selector
     * @param array $options
     */
    public function __construct($selector, $options = array())
    {
        $this->selector = $selector;
        $this->options = $options;
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
     * Set parents
     *
     * @param array $parents
     * @return Element
     */
    public function setParents(array $parents)
    {
        $this->options['parents'] = $parents;

        return $this;
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
     * @param array $options
     */
    public function addOptions(array $options)
    {
        $this->options = array_replace_recursive($this->options, $options);
    }

    /**
     * @param $plugin
     * @return bool
     */
    public function hasPlugin($plugin)
    {
        return array_key_exists('plugins', $this->options) && array_key_exists($plugin, $this->options['plugins']);
    }

    /**
     * @param $plugin
     * @param $pluginData
     */
    public function addPlugin($plugin, $pluginData)
    {
        if (!$this->hasPlugin($plugin)) {
            if (!array_key_exists('plugins', $this->options)) {
                $this->options['plugins'] = array();
            }
            $this->options['plugins'][$plugin] = $pluginData;
        }
    }
} 