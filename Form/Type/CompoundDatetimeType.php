<?php

namespace ITE\FormBundle\Form\Type;

use ITE\FormBundle\Form\DataTransformer\DatetimeToDateAndTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CompoundDatetimeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CompoundDatetimeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['date_options']['required'] = $options['required'];
        //$options['date_options']['model_timezone'] = $options['model_timezone'];
        //$options['date_options']['view_timezone'] = $options['view_timezone'];

        $options['time_options']['required'] = $options['required'];
        $options['time_options']['model_timezone'] = $options['model_timezone'];
        $options['time_options']['view_timezone'] = $options['view_timezone'];

        if (!isset($options['options']['error_bubbling'])) {
            $options['options']['error_bubbling'] = $options['error_bubbling'];
        }

        $builder
            ->add($options['date_name'], $options['date_type'], array_merge($options['options'], $options['date_options']))
            ->add($options['time_name'], $options['time_type'], array_merge($options['options'], $options['time_options']))
            ->addViewTransformer(new DatetimeToDateAndTimeTransformer(
                $options['date_name'],
                $options['time_name'],
                $options['model_timezone'],
                $options['view_timezone']
            ))
        ;
    }

    //public function buildView(FormView $view, FormInterface $form, array $options)
    //{
    //    parent::buildView($view, $form, $options);
    //}
    //
    //public function finishView(FormView $view, FormInterface $form, array $options)
    //{
    //    parent::finishView($view, $form, $options);
    //}

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'options' => [],
            'date_name' => 'date',
            'date_type' => 'date',
            'date_options' => [],
            'time_name' => 'time',
            'time_type' => 'time',
            'time_options' => [],
            'model_timezone' => null,
            'view_timezone' => null,
            'by_reference' => false,
            'data_class' => null,
            'error_bubbling' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'compound_datetime';
    }
}
