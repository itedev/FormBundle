<?php

namespace ITE\FormBundle\Form\Builder;

use ITE\FormBundle\FormAccess\FormAccessorInterface;
use Symfony\Component\Form\FormBuilderInterface as BaseFormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Interface FormBuilderInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormBuilderInterface extends BaseFormBuilderInterface
{
    /**
     * @return FormAccessorInterface
     */
    public function getFormAccessor();

    /**
     * @param int|string|FormBuilderInterface $child
     * @param string|FormTypeInterface $type
     * @param array $options
     * @return FormBuilderInterface
     */
    public function add($child, $type = null, array $options = []);

    /**
     * @param int|string|FormBuilderInterface $child
     * @param string|array $parents
     * @param string|FormTypeInterface $type
     * @param array $options
     * @param callable $callback
     * @return FormBuilderInterface
     */
    public function addHierarchical($child, $parents, $type = null, array $options = [], $callback = null);

    /**
     * @param int|string|FormBuilderInterface $child
     * @param string|FormTypeInterface $type
     * @param callable $callback
     * @return FormBuilderInterface
     */
    public function addDataAware($child, $type = null, $callback = null);

    /**
     * @param string $name
     * @param string $type
     * @param $callback|null $modifier
     * @return FormBuilderInterface
     */
    public function replaceType($name, $type, $callback = null);

    /**
     * @param string $name
     * @param callable $callback
     * @return FormBuilderInterface
     */
    public function replaceOptions($name, $callback);
}
