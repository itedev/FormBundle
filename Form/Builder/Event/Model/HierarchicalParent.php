<?php

namespace ITE\FormBundle\Form\Builder\Event\Model;

use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Util\FormUtil;

/**
 * Class HierarchicalParent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class HierarchicalParent
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var mixed $data
     */
    private $data;

    /**
     * @var FormInterface $form
     */
    private $form;

    /**
     * @var string $originator
     */
    private $originator;

    /**
     * @param string $name
     * @param mixed $data
     * @param FormInterface $form
     * @param string|null $originator
     */
    public function __construct($name, $data, FormInterface $form = null, $originator = null)
    {
        $this->name = $name;
        $this->data = $data;
        $this->form = $form;
        $this->originator = $originator;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return bool
     */
    public function isOriginator()
    {
        return $this->originator === FormUtils::getFullName($this->form);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return FormUtil::isEmpty($this->data);
    }

    /**
     * @return bool
     */
    public function isNotEmpty()
    {
        return !FormUtil::isEmpty($this->data);
    }
}