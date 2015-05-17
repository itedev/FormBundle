<?php

namespace ITE\FormBundle\SF;

use ITE\FormBundle\SF\Form\FormView;

/**
 * Class FormBag
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormBag
{
    private $forms = [];

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->forms);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return FormView|mixed
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->forms[$name] : $default;
    }

    /**
     * @param string $name
     * @param FormView $view
     * @return FormView
     */
    public function add($name, FormView $view)
    {
        if (!$this->has($name)) {
            $this->forms[$name] = $view;
        }

        return $this->get($name);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->forms);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_map(function(FormView $view) {
            return $view->toArray();
        }, $this->forms);
    }
}