<?php

namespace ITE\FormBundle\Form\Doctrine\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class Select2AjaxEntityType
 * @package ITE\FormBundle\Form\Doctrine\Type
 */
class Select2AjaxEntityType extends AbstractType
{
    /**
     * @var array $extras
     */
    protected $extras;

    /**
     * @var array $options
     */
    protected $options;

    /**
     * @param $extras
     * @param $options
     */
    public function __construct($extras, $options)
    {
        $this->extras = array_merge_recursive($extras, array(
            'ajax' => true
        ));
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'extras' => array(),
            'plugin_options' => array(),
            'error_bubbling' => false,
        ));
        $resolver->setAllowedTypes(array(
            'extras' => array('array'),
            'plugin_options' => array('array'),
        ));
    }

    /**
     * {@inheritdoc}
     */
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
            'extras' => array_merge_recursive($this->extras, $options['extras'], array(
                'ajax' => true
            )),
            'options' => array_merge_recursive($this->options, $options['plugin_options'], array(
                'ajax' => array(
                    'url' => $options['url'],
                )
            ))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_ajax_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_select2_ajax_entity';
    }
}