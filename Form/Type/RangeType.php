<?php

namespace ITE\FormBundle\Form\Type;

use ITE\FormBundle\Form\DataTransformer\RangeToArrayTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RangeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class RangeType extends AbstractRangeType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Overwrite required option for child fields
        $options['from_options']['required'] = $options['required'];
        $options['to_options']['required'] = $options['required'];

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        $builder
            ->add($options['from_name'], $options['type'], array_merge($options['options'], $options['from_options']))
            ->add($options['to_name'], $options['type'], array_merge($options['options'], $options['to_options']))
            ->addViewTransformer(new RangeToArrayTransformer($options['class'], $options['from_name'], $options['to_name']))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        // need to call parent method!
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'type' => 'text',
            'options' => [],
            'from_options' => [],
            'to_options' => [],
            'from_name' => 'from',
            'to_name' => 'to',
            'error_bubbling' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_range';
    }
}