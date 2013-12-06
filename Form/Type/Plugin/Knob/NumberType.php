<?php

namespace ITE\FormBundle\Form\Type\Plugin\Knob;

use ITE\FormBundle\SF\SFForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class NumberType
 * @package ITE\FormBundle\Form\Type\Plugin\Knob
 */
class NumberType extends AbstractType
{
    /**
     * @var array $options
     */
    protected $options;

    /**
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'plugin_options' => array(),
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = array();
        }
        $view->vars['plugins'][SFForm::PLUGIN_KNOB] = array(
            'extras' => (object) array(),
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'number';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_knob_number';
    }
}