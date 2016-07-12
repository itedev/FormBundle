<?php

namespace ITE\FormBundle\Form;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface as BaseFormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface FormInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormInterface extends BaseFormInterface
{
    /**
     * @return mixed
     */
    public function getRawModelData();

    /**
     * @param mixed $modelData
     * @return $this
     */
    public function setRawModelData($modelData);

    /**
     * @return mixed
     */
    public function getRawNormData();

    /**
     * @param mixed $normData
     * @return $this
     */
    public function setRawNormData($normData);

    /**
     * @return mixed
     */
    public function getRawViewData();

    /**
     * @param mixed $viewData
     * @return $this
     */
    public function setRawViewData($viewData);

    /**
     * @return FormInterface[]
     */
    public function getRawChildren();

    /**
     * @param FormInterface[] $children
     * @return $this
     */
    public function setRawChildren($children);

    /**
     * @param FormInterface|null $parent
     * @return $this
     */
    public function setRawParent(FormInterface $parent = null);

    /**
     * @param bool $submitted
     * @return $this
     */
    public function setRawSubmitted($submitted);

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setRawOption($name, $value);

    /**
     * @param string $name
     * @return $this
     */
    public function unsetRawOption($name);

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setRawAttribute($name, $value);

    ///**
    // * @param EventDispatcherInterface $ed
    // * @return $this
    // */
    //public function setRawEventDispatcher(EventDispatcherInterface $ed);

    /**
     * @return mixed
     */
    public function getOriginalData();

    /**
     * @param Request $request
     * @return bool
     */
    public function isHierarchicalOriginator(Request $request);

    /**
     * @param FormInterface|string|int
     * @param string|null
     * @param array
     * @return FormInterface
     */
    public function add($child, $type = null, array $options = []);

    /**
     * @param string $name
     * @param string $type
     * @param callable|null $modifier
     * @return FormInterface
     */
    public function replaceType($name, $type, $modifier = null);

    /**
     * @param string $name
     * @param callable $modifier
     * @return FormInterface
     */
    public function replaceOptions($name, $modifier);

    /**
     * @param FormInterface|string|int $child
     * @param string|array $parents
     * @param string|null $type
     * @param array $options
     * @param callable $callback
     * @return FormInterface
     */
    public function addHierarchical($child, $parents, $type = null, array $options = [], $callback = null);
}
