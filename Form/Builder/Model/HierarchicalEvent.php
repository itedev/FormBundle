<?php

namespace ITE\FormBundle\Form\Builder\Model;

use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Class HierarchicalEvent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class HierarchicalEvent
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var string
     */
    protected $originator;

    /**
     * @var array
     */
    protected $parents = [];

    /**
     * @var int|string|FormBuilderInterface
     */
    protected $name;

    /**
     * @var string|FormTypeInterface
     */
    protected $type;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param FormInterface $form
     * @param array $parents
     * @param array $options
     * @param string|null $originator
     */
    public function __construct(FormInterface $form, array $parents, array $options, $originator = null)
    {
        $this->form = $form;
        $this->parents = $parents;
        $this->options = $options;
        $this->originator = $originator;
    }

    /**
     * Get form
     *
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get originator
     *
     * @return string
     */
    public function getOriginator()
    {
        return $this->originator;
    }

    /**
     * Get parents
     *
     * @return array
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * Get name
     *
     * @return int|string|FormBuilderInterface
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get type
     *
     * @return string|FormTypeInterface
     */
    public function getType()
    {
        return $this->type;
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
     * @param mixed $value
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function unsetOption($name)
    {
        if (array_key_exists($name, $this->options)) {
            unset($this->options[$name]);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOriginator()
    {
        return null !== $this->originator;
    }

    /**
     * @return bool
     */
    public function isOriginator()
    {
        if (!$this->hasOriginator()) {
            return false;
        }

        $parentForm = $this->form->getParent();
        foreach ($this->parents as $parent => $parentData) {
            if ($this->originator === FormUtils::getFullName($parentForm->get($parent))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isSubmitted()
    {
        return $this->hasOriginator();
    }
}