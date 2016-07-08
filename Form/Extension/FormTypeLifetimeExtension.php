<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\SF\Form\ClientFormTypeExtensionInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeLifetimeExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeLifetimeExtension extends AbstractTypeExtension implements ClientFormTypeExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['build_form'])) {
            call_user_func_array($options['build_form'], func_get_args());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['build_view'])) {
            call_user_func_array($options['build_view'], func_get_args());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['finish_view'])) {
            call_user_func_array($options['finish_view'], func_get_args());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['build_client_view'])) {
            call_user_func_array($options['build_client_view'], func_get_args());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional([
            'build_form',
            'build_view',
            'finish_view',
            'build_client_view',
        ]);
        $resolver->setAllowedTypes([
            'build_form' => ['callable'],
            'build_view' => ['callable'],
            'finish_view' => ['callable'],
            'build_client_view' => ['callable'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
