<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeOriginalConfigurationExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeOriginalConfigurationExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional([
           'skip_interceptors',
        ]);
        $resolver->setDefaults([
            'original_type' => null,
            'original_options' => [],
        ]);
        $resolver->setAllowedTypes([
            'original_options' => ['array'],
            'skip_interceptors' => ['bool'],
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
