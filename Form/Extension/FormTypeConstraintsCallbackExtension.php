<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\Form\FormInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeConstraintsCallbackExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeConstraintsCallbackExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['constraints_callback'])) {
            return;
        }

        $constraintsCallback = $options['constraints_callback'];
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($constraintsCallback) {
            /** @var FormInterface $form */
            $form = $event->getForm();

            $constraints = call_user_func_array($constraintsCallback, [$form]);
            $constraints = array_merge($form->getConfig()->getOption('constraints'), $constraints);

            $form->setRawOption('constraints', $constraints);
        }, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional([
            'constraints_callback',
        ]);
        $resolver->setAllowedTypes([
            'constraints_callback' => ['callable'],
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
