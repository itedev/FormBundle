<?php

namespace ITE\FormBundle\Form\Extension\Component\Validation;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeValidationExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeValidationExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!$form->isRoot()) {
            return;
        }

        $constraintConversion = $options['constraint_conversion'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $constraintsNormalizer = function (Options $options, $constraints) {
            return is_object($constraints) ? [$constraints] : (array) $constraints;
        };

        $resolver->setDefaults([
            'client_constraints' => [],
            'constraint_conversion' => false,
        ]);
        $resolver->setNormalizers([
            'client_constraints' => $constraintsNormalizer,
        ]);
        $resolver->setAllowedTypes([
            'constraint_conversion' => ['bool'],
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