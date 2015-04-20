<?php

namespace ITE\FormBundle\SF;

/**
 * Class Element
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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
    protected $options = [];

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
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function addOptions(array $options)
    {
        $this->options = array_replace_recursive($this->options, $options);

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return null
     */
    public function getOption($name, $defaultValue = null)
    {
        return $this->hasOption($name) ? $this->options[$name] : $defaultValue;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param string $plugin
     * @return bool
     */
    public function hasPlugin($plugin)
    {
        return array_key_exists('plugins', $this->options) && array_key_exists($plugin, $this->options['plugins']);
    }

    /**
     * @param string $plugin
     * @param mixed $pluginData
     * @return $this
     */
    public function addPlugin($plugin, $pluginData)
    {
        if (!$this->hasPlugin($plugin)) {
            if (!array_key_exists('plugins', $this->options)) {
                $this->options['plugins'] = [];
            }
            $this->options['plugins'][$plugin] = $pluginData;
        }

        return $this;
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
     * @param string $parent
     * @return bool
     */
    public function hasParent($parent)
    {
        return array_key_exists('parents', $this->options) && in_array($parent, $this->options['parents']);
    }

    /**
     * @param string $parent
     * @return $this
     */
    public function addParent($parent)
    {
        if (!$this->hasParent($parent)) {
            if (!array_key_exists('parents', $this->options)) {
                $this->options['parents'] = [];
            }

            $this->options['parents'][] = $parent;
        }

        return $this;
    }

    /**
     * @param array $children
     * @return $this
     */
    public function setChildren(array $children)
    {
        $this->options['children'] = $children;

        return $this;
    }

    /**
     * @param $child
     * @return bool
     */
    public function hasChild($child)
    {
        return array_key_exists('children', $this->options) && in_array($child, $this->options['children']);
    }

    /**
     * @param string $child
     * @return $this
     */
    public function addChild($child)
    {
        if (!$this->hasChild($child)) {
            if (!array_key_exists('children', $this->options)) {
                $this->options['children'] = [];
            }

            $this->options['children'][] = $child;
        }

        return $this;
    }
} 