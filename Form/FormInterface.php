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
     * @param null $formModifier
     * @return FormInterface
     */
    public function addHierarchical($child, $parents, $type = null, array $options = [], $formModifier = null);
}
