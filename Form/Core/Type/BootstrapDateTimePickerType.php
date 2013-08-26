<?php

namespace ITE\FormBundle\Form\Core\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BootstrapDateTimePickerType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'plugin_options' => array(),
            'format' => 'yyyy-MM-dd HH:mm:ss',
            'extras' => array(),
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
            'extras' => array('array'),
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['element_data'] = array(
            'extras' => (object) $options['extras'],
            'options' => array_merge_recursive($options['plugin_options'], array(
                'weekStart' => 1,
                'format' => strtr($options['format'], array(
                    'h' => 'H',
                    'H' => 'h',
                    'a' => 'PP'
                )),
                'pickSeconds' => $options['with_seconds']
            ))
        );
    }

    public function getParent()
    {
        return 'datetime';
    }

    public function getName()
    {
        return 'ite_bootstrap_datetime_picker';
    }
}