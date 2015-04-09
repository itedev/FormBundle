<?php

namespace ITE\FormBundle\Form\Builder;

use Symfony\Component\Form\FormBuilderInterface as BaseFormBuilderInterface;

/**
 * Interface FormBuilderInterface
 * @package ITE\FormBundle\Form\Builder
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
     * @param null $type
     * @param array $options
     * @param array|string $parentNames
     * @param callable $formModifier
     * @return FormBuilderInterface
     */
    public function addHierarchical($child, $type = null, array $options = array(), $parentNames = null, $formModifier = null);
}