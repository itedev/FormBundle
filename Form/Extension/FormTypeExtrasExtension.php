<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeExtrasExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeExtrasExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional([
            'extras',
        ]);
        $resolver->setAllowedTypes([
            'extras' => ['array'],
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['extras'])) {
            $view->vars['extras'] = $options['extras'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}