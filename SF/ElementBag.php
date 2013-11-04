<?php

namespace ITE\FormBundle\SF;

/**
 * Class ElementBag
 * @package ITE\FormBundle\SF
 */
class ElementBag
{
    /**
     * @var array
     */
    protected $plugins = array();

    /**
     * @var array
     */
    protected $elements = array();

    /**
     * @param $plugin
     * @return bool
     */
    public function hasPlugin($plugin)
    {
        return array_key_exists($plugin, $this->plugins);
    }

    /**
     * @return array
     */
    public function getPlugins()
    {
        return array_keys($this->plugins);
    }

    /**
     * @param $plugin
     * @return array
     */
    public function getPluginElements($plugin)
    {
        return $this->hasPlugin($plugin) ? $this->plugins[$plugin] : array();
    }

    /**
     * @param $selector
     * @return bool
     */
    public function hasElement($selector)
    {
        return array_key_exists($selector, $this->elements);
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return array_keys($this->elements);
    }

    /**
     * @param $plugin
     * @param $selector
     * @param $data
     */
    public function addElement($plugin, $selector, $data)
    {
        if (!$this->hasPlugin($plugin)) {
            $this->plugins[$plugin] = array();
        }
        $this->plugins[$plugin][$selector] = $data;
        $this->elements[$selector] = $plugin;
    }

    /**
     * @param $selector
     * @return null
     */
    public function getElementOptions($selector)
    {
        if (!$this->hasElement($selector)) {
            return null;
        }
        return $this->plugins[$this->elements[$selector]][$selector]['options'];
    }

    /**
     * @return array
     */
    public function peekAll()
    {
        return $this->plugins;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->plugins);
    }

}