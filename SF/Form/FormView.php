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
    private $options = [];

    /**
     * @var FormView|null
     */
    private $parent;

    /**
     * @var array|FormView[]
     */
    private $children = [];

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
     * @return FormView
     */
    public function getRoot()
    {
        return null !== $this->parent ? $this->getRoot() : $this;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return null !== $this->parent;
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
            $options[$name] = 'prototype' === $name
                ? $value->toArray()
                : $value;
        }

        return [
            'options' => $options,
            'children' => $children
        ];
    }
}