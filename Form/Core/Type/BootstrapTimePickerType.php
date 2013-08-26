<?php

namespace ITE\FormBundle\Form\Core\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BootstrapTimePickerType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'plugin_options' => array(),
            'extras' => array(),
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
            'extras' => array('array'),
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $format = 'hh';
        if ($options['with_minutes']) {
            $format .= ':mm';
        }
        if ($options['with_seconds']) {
            $format .= ':ss';
        }

        $view->vars['element_data'] = array(
            'extras' => (object) $options['extras'],
            'options' => array_merge_recursive($options['plugin_options'], array(
                'pickDate' => false,
                'format' => $format,
                'pickSeconds' => $options['with_seconds']
            ))
        );
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type'] = 'text';
    }

    public function getParent()
    {
        return 'time';
    }

    public function getName()
    {
        return 'ite_bootstrap_time_picker';
    }
}