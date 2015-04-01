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
     * @var string|null $labelPath
     */
    protected $labelPath = null;

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
     * Get labelPath
     *
     * @return null
     */
    public function getLabelPath()
    {
        return $this->labelPath;
    }

    /**
     * Set labelPath
     *
     * @param null $labelPath
     * @return EntityConverter
     */
    public function setLabelPath($labelPath)
    {
        $this->labelPath = $labelPath;

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