<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\SF\Form\FormViewBuilderInterface;
use ITE\FormBundle\SF\SFFormExtensionInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeFormViewExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeFormViewExtension extends AbstractTypeExtension
{
    /**
     * @var SFFormExtensionInterface
     */
    protected $sfForm;

    /**
     * @var FormViewBuilderInterface
     */
    protected $builder;

    /**
     * @param SFFormExtensionInterface $sfForm
     * @param FormViewBuilderInterface $builder
     */
    public function __construct(SFFormExtensionInterface $sfForm, FormViewBuilderInterface $builder)
    {
        $this->sfForm = $sfForm;
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!$form->isRoot() || null !== $view->parent) {
            return;
        }

//        $clientView = $this->builder->createView($view, $form);
//
//        $this->sfForm->getFormBag()->add($form->getName(), $clientView);
//        $a = 1;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}