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
