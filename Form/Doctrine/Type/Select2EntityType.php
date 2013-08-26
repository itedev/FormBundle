<?php

namespace ITE\FormBundle\Form\Doctrine\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Select2EntityType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
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
        $view->vars['element_data'] = array(
            'extras' => (object) $options['extras'],
            'options' => (object) $options['plugin_options']
        );
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'ite_select2_entity';
    }
}