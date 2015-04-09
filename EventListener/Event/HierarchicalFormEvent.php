<?php

namespace ITE\FormBundle\EventListener\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;

/**
 * Class HierarchicalFormEvent
 * @package ITE\FormBundle\EventListener\Event
 */
class HierarchicalFormEvent extends Event
{
    /**
     * @var FormInterface $form
     */
    protected $form;

    /**
     * @var string $parentName
     */
    protected $parentName;

    /**
     * @var string $childName
     */
    protected $childName;

    /**
     * @var mixed $parentData
     */
    protected $parentData;

    /**
     * @param FormInterface $form
     * @param string $childName
     * @param string $parentName
     * @param mixed $parentData
     */
    public function __construct(FormInterface $form, $childName, $parentName, $parentData)
    {
        $this->setForm($form);
        $this->setChildName($childName);
        $this->setParentName($parentName);
        $this->setParentData($parentData);
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
     * Set form
     *
     * @param FormInterface $form
     * @return HierarchicalFormEvent
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get childName
     *
     * @return string
     */
    public function getChildName()
    {
        return $this->childName;
    }

    /**
     * Set childName
     *
     * @param string $childName
     * @return HierarchicalFormEvent
     */
    public function setChildName($childName)
    {
        $this->childName = $childName;

        return $this;
    }

    /**
     * Get parentData
     *
     * @return mixed
     */
    public function getParentData()
    {
        return $this->parentData;
    }

    /**
     * Set parentData
     *
     * @param mixed $parentData
     * @return HierarchicalFormEvent
     */
    public function setParentData($parentData)
    {
        $this->parentData = $parentData;

        return $this;
    }

    /**
     * Get parentName
     *
     * @return string
     */
    public function getParentName()
    {
        return $this->parentName;
    }

    /**
     * Set parentName
     *
     * @param string $parentName
     * @return HierarchicalFormEvent
     */
    public function setParentName($parentName)
    {
        $this->parentName = $parentName;

        return $this;
    }


}