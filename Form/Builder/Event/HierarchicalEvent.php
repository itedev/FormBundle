<?php

namespace ITE\FormBundle\Form\Builder\Event;

use ITE\FormBundle\Form\Builder\Event\Model\HierarchicalParent;
use ITE\FormBundle\Form\Builder\Event\Model\HierarchicalParentCollection;
use ITE\FormBundle\FormAccess\FormAccess;
use ITE\FormBundle\FormAccess\FormAccessor;
use ITE\FormBundle\FormAccess\FormAccessorInterface;
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
     * @var HierarchicalParentCollection
     */
    protected $parents;

    /**
     * @var bool $submitted
     */
    protected $submitted;

    /**
     * @var array|null
     */
    protected $originator;

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
     * @var mixed
     */
    protected $data;

    /**
     * @param FormInterface $form
     * @param string|FormInterface $type
     * @param array|HierarchicalParent[] $parents
     * @param array $options
     * @param mixed $data
     * @param bool $submitted
     * @param array|null $originator
     */
    public function __construct(
        FormInterface $form,
        $type,
        array $parents,
        array $options,
        $data,
        $submitted = false,
        array $originator = null
    ) {
        $this->form = $form;
        $this->type = $type;
        $this->parents = new HierarchicalParentCollection($parents);
        $this->options = $options;
        $this->data = $data;
        $this->submitted = $submitted;
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
     * @return array|null
     */
    public function getOriginator()
    {
        return $this->originator;
    }

    /**
     * @return string|null
     */
    public function getSingleOriginator()
    {
        if (!$this->hasOriginator()) {
            return null;
        }

        return $this->originator[0];
    }

    /**
     * @return bool
     */
    public function hasOriginator()
    {
        return null !== $this->originator;
    }

    /**
     * Get parents
     *
     * @return HierarchicalParentCollection
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
     * Set type
     *
     * @param string|FormTypeInterface $type
     *
     * @return HierarchicalEvent
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * Get data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
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
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getOption($name, $defaultValue = null)
    {
        return array_key_exists($name, $this->options) ? $this->options[$name] : $defaultValue;
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
        return $this->setOption('hierarchical_data', $data);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $attr = $this->getOption('attr', []);
        $attr[$name] = $value;
        $this->setOption('attr', $attr);

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function unsetAttribute($name)
    {
        $attr = $this->getOption('attr', []);
        if (array_key_exists($name, $attr)) {
            unset($attr[$name]);
            $this->setOption('attr', $attr);
        }

        return $this;
    }

    /**
     * @param string $parentName
     * @return HierarchicalParent|null
     */
    public function getParent($parentName)
    {
        return $this->parents->get($parentName);
    }

    /**
     * @param string $parentName
     * @return bool
     */
    public function hasParent($parentName)
    {
        return $this->parents->has($parentName);
    }

    /**
     * @return bool
     */
    public function isAffected()
    {
        if (!$this->hasOriginator()) {
            return false;
        }

        foreach ($this->parents as $parentName => $parent) {
            /** @var HierarchicalParent $parent */
            if ($parent->isOriginator()) {
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
        return $this->submitted;
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
}
