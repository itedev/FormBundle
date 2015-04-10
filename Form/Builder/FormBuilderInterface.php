<?php

namespace ITE\FormBundle\Form\Builder;

use Symfony\Component\Form\FormBuilderInterface as BaseFormBuilderInterface;

/**
 * Interface FormBuilderInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormBuilderInterface extends BaseFormBuilderInterface
{
    /**
     * @param int|string|FormBuilderInterface $child
     * @param null $type
     * @param array $options
     * @return FormBuilderInterface
     */
    public function add($child, $type = null, array $options = array());

    /**
     * @param $child
     * @param array|string $parents
     * @param null $type
     * @param array $options
     * @param callable $formModifier
     * @return FormBuilderInterface
     */
    public function addHierarchical($child, $parents, $type = null, array $options = array(), $formModifier = null);
}