<?php

namespace ITE\FormBundle\SF\Form;

/**
 * Class FormView
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormView
{
    /**
     * @var array
     */
    public $options = [];

    /**
     * @var FormView|null
     */
    public $parent;

    /**
     * @var array|FormView[]
     */
    public $children = [];

    /**
     * @param FormView $parent
     */
    public function __construct(FormView $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return FormView|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @param FormView|null $parent
     * @return $this
     */
    public function setParent(FormView $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get children
     *
     * @return array|FormView[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param string $name
     * @param FormView $child
     * @return $this
     */
    public function addChild($name, FormView $child)
    {
        $child->setParent($this);
        $this->children[$name] = $child;

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
     * @return int
     */
    public function count()
    {
        return count($this->children);
    }
}