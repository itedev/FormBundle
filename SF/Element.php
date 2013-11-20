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
     * @var array $plugins
     */
    protected $plugins = array();

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
     * Get plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
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
     * Set parents
     *
     * @param array $parents
     * @return Element
     */
    public function setParents(array $parents)
    {
        $this->parents = $parents;

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
        return array_key_exists($plugin, $this->plugins);
    }

    /**
     * @param $plugin
     * @param $pluginData
     */
    public function addPlugin($plugin, $pluginData)
    {
        if (!$this->hasPlugin($plugin)) {
            $this->plugins[$plugin] = $pluginData;
        }
    }
} 