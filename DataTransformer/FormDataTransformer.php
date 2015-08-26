<?php

namespace ITE\FormBundle\DataTransformer;

use Symfony\Component\Form\Extension\Core\DataTransformer\DataTransformerChain;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class FormDataTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormDataTransformer implements FormDataTransformerInterface
{
    /**
     * @var FormFactoryInterface $formFactory
     */
    private $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getModelTransformer($form, array $options = [])
    {
        if ($form instanceof FormInterface) {
            $modelTransformers = $form->getConfig()->getModelTransformers();
        } elseif ($form instanceof FormConfigInterface) {
            $modelTransformers = $form->getModelTransformers();
        } else {
            $form = $this->formFactory->create($form, null, $options);
            $modelTransformers = $form->getConfig()->getModelTransformers();
        }

        return new DataTransformerChain($modelTransformers);
    }

    /**
     * {@inheritdoc}
     */
    public function getViewTransformer($form, array $options = [])
    {
        if ($form instanceof FormInterface) {
            $viewTransformers = $form->getConfig()->getViewTransformers();
        } elseif ($form instanceof FormConfigInterface) {
            $viewTransformers = $form->getViewTransformers();
        } else {
            $form = $this->formFactory->create($form, null, $options);
            $viewTransformers = $form->getConfig()->getViewTransformers();
        }

        return new DataTransformerChain($viewTransformers);
    }

    /**
     * {@inheritdoc}
     */
    public function modelToNorm($value, $form, array $options = [])
    {
        $modelTransformer = $this->getModelTransformer($form, $options);

        return $modelTransformer->transform($value);
    }

    /**
     * {@inheritdoc}
     */
    public function normToModel($value, $form, array $options = [])
    {
        $modelTransformer = $this->getModelTransformer($form, $options);

        return $modelTransformer->reverseTransform($value);
    }

    /**
     * {@inheritdoc}
     */
    public function normToView($value, $form, array $options = [])
    {
        $viewTransformer = $this->getViewTransformer($form, $options);

        return $viewTransformer->transform($value);
    }

    /**
     * {@inheritdoc}
     */
    public function viewToNorm($value, $form, array $options = [])
    {
        $viewTransformer = $this->getViewTransformer($form, $options);

        return $viewTransformer->reverseTransform($value);
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value, $form, array $options = [])
    {
        $modelTransformer = $this->getModelTransformer($form, $options);
        $viewTransformer = $this->getViewTransformer($form, $options);

        $value = $modelTransformer->transform($value);
        $value = $viewTransformer->transform($value);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value, $form, array $options = [])
    {
        $modelTransformer = $this->getModelTransformer($form, $options);
        $viewTransformer = $this->getViewTransformer($form, $options);

        $value = $viewTransformer->reverseTransform($value);
        $value = $modelTransformer->reverseTransform($value);

        return $value;
    }

}