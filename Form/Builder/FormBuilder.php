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
}