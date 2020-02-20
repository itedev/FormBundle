<?php

namespace ITE\FormBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
class EntityConverter extends ConfigurationAnnotation
{
    /**
     * @var string $alias
     */
    protected $alias = 'default';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var bool
     */
    protected $multiple = true;

    /**
     * @var callable
     */
    protected $entityOptionsCallback;

    /**
     * @var array
     */
    protected $entityOptionsCallbackArguments = [];

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return EntityConverter
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

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
     * Set Options
     *
     * @param array $options
     * @return EntityConverter
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get multiple
     *
     * @return bool
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * Set multiple
     *
     * @param bool $multiple
     *
     * @return EntityConverter
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Get entityOptionsCallback
     *
     * @return callable
     */
    public function getEntityOptionsCallback()
    {
        return $this->entityOptionsCallback;
    }

    /**
     * Set entityOptionsCallback
     *
     * @param callable $entityOptionsCallback
     *
     * @return EntityConverter
     */
    public function setEntityOptionsCallback($entityOptionsCallback)
    {
        $this->entityOptionsCallback = $entityOptionsCallback;

        return $this;
    }

    /**
     * Get entityOptionsCallbackArguments
     *
     * @return array
     */
    public function getEntityOptionsCallbackArguments()
    {
        return $this->entityOptionsCallbackArguments;
    }

    /**
     * Set entityOptionsCallbackArguments
     *
     * @param array $entityOptionsCallbackArguments
     * @return EntityConverter
     */
    public function setEntityOptionsCallbackArguments(array $entityOptionsCallbackArguments)
    {
        $this->entityOptionsCallbackArguments = $entityOptionsCallbackArguments;

        return $this;
    }

    /**
     * @param $alias
     */
    public function setValue($alias)
    {
        $this->setAlias($alias);
    }

    /**
     * {@inheritdoc}
     */
    public function getAliasName()
    {
        return 'entity_converter';
    }

    /**
     * {@inheritdoc}
     */
    public function allowArray()
    {
        return false;
    }
}
