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
    public function add($child, $type = null, array $options = array());

    /**
     * @param int|string|FormBuilderInterface $child
     * @param string|array $parents
     * @param string|FormTypeInterface $type
     * @param array $options
     * @param null $formModifier
     * @return FormBuilderInterface
     */
    public function addHierarchical($child, $parents, $type = null, array $options = array(), $formModifier = null);

    /**
     * @param int|string|FormBuilderInterface $child
     * @param string|FormTypeInterface $type
     * @param callable $formModifier
     * @return FormBuilderInterface
     */
    public function addDataAware($child, $type = null, $formModifier = null);

}