<?php

namespace ITE\FormBundle\Form;

use ITE\FormBundle\Form\Builder\ButtonBuilder;
use ITE\FormBundle\Form\Builder\FormBuilder;
use ITE\FormBundle\Form\Builder\SubmitButtonBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\ButtonTypeInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\ResolvedFormType as BaseResolvedFormType;
use Symfony\Component\Form\SubmitButtonTypeInterface;

/**
 * Class ResolvedFormType
 * @package ITE\FormBundle\Form
 */
class ResolvedFormType extends BaseResolvedFormType
{
    /**
     * {@inheritdoc}
     */
    public function createView(FormInterface $form, FormView $parent = null)
    {
        $options = $form->getConfig()->getOptions();

        $view = $this->newView($parent);

        $this->buildView($view, $form, $options);

        $orderer = new FormOrderer();
        foreach ($orderer->order($form) as $name) {
            $view->children[$name] = $form[$name]->createView($view);
        }

        $this->finishView($view, $form, $options);

        return $view;
    }

    /**
     * {@inheritdoc}
     */
    protected function newBuilder($name, $dataClass, FormFactoryInterface $factory, array $options)
    {
        if ($this->getInnerType() instanceof ButtonTypeInterface) {
            return new ButtonBuilder($name, $options);
        }

        if ($this->getInnerType() instanceof SubmitButtonTypeInterface) {
            return new SubmitButtonBuilder($name, $options);
        }

        return new FormBuilder($name, $dataClass, new EventDispatcher(), $factory, $options);
    }
}