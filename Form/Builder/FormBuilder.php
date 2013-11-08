<?php

namespace ITE\FormBundle\Form\Builder;

use Symfony\Component\Form\FormBuilder as BaseFormBuilder;

/**
 * Class FormBuilder
 * @package ITE\FormBundle\Form\Builder
 */
class FormBuilder extends BaseFormBuilder
{
    /**
     * @var array $extras
     */
    protected $extras;

    /**
     * @param $name
     * @param $type
     */
    public function replaceType($name, $type)
    {
        $field = $this->get($name);
        $options = $field->getOptions();

        $this->add($name, $type, $options);
    }

    /**
     * @param $name
     * @param $options
     */
    public function replaceOptions($name, $options)
    {
        $field = $this->get($name);
        $currentOptions = $field->getOptions();
        $type = $field->getType()->getName();

        $options = array_replace_recursive($currentOptions, $options);

        $this->add($name, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * {@inheritdoc}
     */
    public function hasExtra($name)
    {
        return array_key_exists($name, $this->extras);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtra($name, $default = null)
    {
        return array_key_exists($name, $this->extras) ? $this->extras[$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtra($name, $value)
    {
        $this->extras[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtras(array $extras)
    {
        $this->extras = $extras;

        return $this;
    }
}