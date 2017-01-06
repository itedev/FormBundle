<?php

namespace ITE\FormBundle\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * Interface FormDataTransformerInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormDataTransformerInterface
{
    /**
     * @param FormInterface|FormConfigInterface|FormTypeInterface|ResolvedFormTypeInterface|string $form
     * @param array $options
     * @return DataTransformerInterface
     */
    public function getModelTransformer($form, array $options = []);

    /**
     * @param FormInterface|FormConfigInterface|FormTypeInterface|ResolvedFormTypeInterface|string $form
     * @param array $options
     * @return DataTransformerInterface
     */
    public function getViewTransformer($form, array $options = []);

    /**
     * @param mixed $value
     * @param FormInterface|FormConfigInterface|FormTypeInterface|ResolvedFormTypeInterface|string $form
     * @param array $options
     * @return mixed
     */
    public function modelToNorm($value, $form, array $options = []);

    /**
     * @param mixed $value
     * @param FormInterface|FormConfigInterface|FormTypeInterface|ResolvedFormTypeInterface|string $form
     * @param array $options
     * @return mixed
     */
    public function normToModel($value, $form, array $options = []);

    /**
     * @param mixed $value
     * @param FormInterface|FormConfigInterface|FormTypeInterface|ResolvedFormTypeInterface|string $form
     * @param array $options
     * @return mixed
     */
    public function normToView($value, $form, array $options = []);

    /**
     * @param mixed $value
     * @param FormInterface|FormConfigInterface|FormTypeInterface|ResolvedFormTypeInterface|string $form
     * @param array $options
     * @return mixed
     */
    public function viewToNorm($value, $form, array $options = []);

    /**
     * @param mixed $value
     * @param FormInterface|FormConfigInterface|FormTypeInterface|ResolvedFormTypeInterface|string $form
     * @param array $options
     * @return mixed
     */
    public function transform($value, $form, array $options = []);

    /**
     * @param mixed $value
     * @param FormInterface|FormConfigInterface|FormTypeInterface|ResolvedFormTypeInterface|string $form
     * @param array $options
     * @return mixed
     */
    public function reverseTransform($value, $form, array $options = []);
}
