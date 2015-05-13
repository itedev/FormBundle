<?php

namespace ITE\FormBundle\Form\Builder\Event;

use ITE\FormBundle\FormAccess\FormAccess;
use ITE\FormBundle\FormAccess\FormAccessor;
use ITE\FormBundle\FormAccess\FormAccessorInterface;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\Util\FormUtil;

/**
 * Class HierarchicalEvent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class HierarchicalEvent
{
    /**
     * @var FormAccessor
     */
    protected $formAccessor;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var string
     */
    protected $originator;

    /**
     * @var ParentCollection
     */
    protected $parents;

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
     * @param FormAccessor|null $formAccessor
     */
    public function __construct(FormInterface $form, array $parents, array $options, $originator = null,
        FormAccessor $formAccessor = null)
    {
        $this->form = $form;
        $this->parents = new ParentCollection($parents);
        $this->options = $options;
        $this->originator = $originator;
        $this->formAccessor = $formAccessor;
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
     * @return ParentCollection
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
     * @param mixed $data
     * @return $this
     */
    public function setData($data)
    {
        return $this->setOption('data', $data);
    }

    /**
     * @param string $parent
     * @return mixed|null
     */
    public function getParent($parent)
    {
        return $this->parents->get($parent);
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
    public function isAffected()
    {
        if (!$this->hasOriginator()) {
            return false;
        }

        $formAccessor = $this->getFormAccessor();
        foreach ($this->parents as $parent => $parentData) {
            $parentForm = $formAccessor->getForm($this->form, $parent);
            if ($this->originator === FormUtils::getFullName($parentForm)) {
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

    /**
     * @return bool
     */
    public function isParentsEmpty()
    {
        return $this->parents->isEmpty();
    }

    /**
     * @return bool
     */
    public function isParentsNotEmpty()
    {
        return $this->parents->isNotEmpty();
    }

    /**
     * @return FormAccessorInterface
     */
    protected function getFormAccessor()
    {
        if (!isset($this->formAccessor)) {
            $this->formAccessor = FormAccess::createFormAccessor();
        }

        return $this->formAccessor;
    }
}