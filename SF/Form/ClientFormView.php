<?php

namespace ITE\FormBundle\SF\Form;

/**
 * Class ClientFormView
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ClientFormView
{
    /**
     * @var array
     */
    private $options = [];

    /**
     * @var ClientFormView|null
     */
    private $parent;

    /**
     * @var array|ClientFormView[]
     */
    private $children = [];

    /**
     * @var array $attributes
     */
    private $attributes = [];

    /**
     * @param ClientFormView $parent
     */
    public function __construct(ClientFormView $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return ClientFormView|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return ClientFormView
     */
    public function getRoot()
    {
        return $this->parent ? $this->parent->getRoot() : $this;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return null === $this->parent;
    }

    /**
     * Set parent
     *
     * @param ClientFormView|null $parent
     * @return $this
     */
    public function setParent(ClientFormView $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get children
     *
     * @return array|ClientFormView[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param string $name
     * @param ClientFormView $child
     * @return $this
     */
    public function addChild($name, ClientFormView $child)
    {
        $child->setParent($this);
        $this->children[$name] = $child;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return ClientFormView|null
     */
    public function getChild($name, $defaultValue = null)
    {
        return $this->hasChild($name) ? $this->children[$name] : $defaultValue;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasChild($name)
    {
        return array_key_exists($name, $this->children);
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
     * @return ClientFormView
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return null
     */
    public function getAttribute($name, $defaultValue = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $defaultValue;
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
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
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
     * @param array $options
     * @return $this
     */
    public function addOptions(array $options)
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * @param $pluginName
     * @param array $pluginOptions
     * @return $this
     */
    public function addPlugin($pluginName, array $pluginOptions)
    {
        $plugins = $this->getOption('plugins', []);
        $plugins[$pluginName] = $pluginOptions;
        $this->setOption('plugins', $plugins);

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $children = [];
        foreach ($this->children as $name => $child) {
            $children[$name] = $child->toArray();
        }

        $options = [];
        foreach ($this->options as $name => $value) {
            $options[$name] = $value instanceof self
                ? $value->toArray()
                : $value;
        }

        return [
            'options' => $options,
            'children' => $children
        ];
    }
}
