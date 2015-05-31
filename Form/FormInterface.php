<?php

namespace ITE\FormBundle\Form;

use Symfony\Component\Form\FormInterface as BaseFormInterface;

/**
 * Interface FormInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormInterface extends BaseFormInterface
{
    /**
     * @param FormInterface|string|int $child
     * @param string|array $parents
     * @param string|null $type
     * @param array $options
     * @param null $formModifier
     * @return FormInterface
     */
    public function addHierarchical($child, $parents, $type = null, array $options = array(), $formModifier = null);
}