<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\Form\DataTransformer\BooleanToInversedTransformer;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CheckboxTypeInversedExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CheckboxTypeInversedExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['inversed']) {
            $builder->addViewTransformer(new BooleanToInversedTransformer(), true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'inversed' => false,
        ]);
        $resolver->setAllowedTypes([
            'inversed' => ['bool'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'checkbox';
    }
}
