<?php

namespace ITE\FormBundle\Form\Doctrine\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Select2AjaxEntityType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'plugin_options' => array(),
            'extras' => array(),
            'error_bubbling' => false,
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
            'extras' => array('array'),
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($form->getData()) {
            $choices = $view->vars['choices'];
            $value = $view->vars['value'];
            $defaultValue = array();

            if ($options['multiple']) {
                // multiple
                $defaultValue = array();
                foreach ($choices as $choice) {
                    /** @var $choice ChoiceView */
                    if ($value == $choice->value) {
                        $defaultValue[] = array(
                            'id' => $choice->value,
                            'text' => $choice->label
                        );
                    }
                }
            } else {
                // single
                foreach ($choices as $choice) {
                    /** @var $choice ChoiceView */
                    if ($value == $choice->value) {
                        $defaultValue = array(
                            'id' => $choice->value,
                            'text' => $choice->label
                        );
                        break;
                    }
                }
            }
            $view->vars['attr']['data-default-value'] = json_encode($defaultValue);
        }

        $view->vars['element_data'] = array(
            'extras' => array_merge_recursive($options['extras'], array(
                'ajax' => true
            )),
            'options' => array_merge_recursive($options['plugin_options'], array(
                'ajax' => array(
                    'url' => $options['url'],
                )
            ))
        );
    }

    public function getParent()
    {
        return 'ite_ajax_entity';
    }

    public function getName()
    {
        return 'ite_select2_ajax_entity';
    }
}