<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapDatetimepicker;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DateType
 * @package ITE\FormBundle\Form\Type\Plugin\BootstrapDatetimepicker
 */
class DateType extends AbstractType
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
            'widget' => 'single_text',
            'plugin_options' => array(),
            'extras' => array(),
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
            'extras' => array('array'),
        ));
        $resolver->setAllowedValues(array(
            'widget' => array('single_text'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['element_data'] = array(
            'extras' => (object) $options['extras'],
            'options' => array_replace_recursive($this->options, $options['plugin_options'], array(
                'format' => strtr($options['format'], array(
                    'a' => 'p', // am/pm marker
                    'm' => 'i', // minute in hour
                    'h' => 'H', // hour in am/pm (1~12)
                    'H' => 'h', // hour in day (0~23)
                    'MMMM' => 'MM', // month in year (September)
                    'MMM' => 'M', // month in year (Sept)
                    'MM' => 'mm', // month in year (09)
                    'M' => 'm', // month in year (9)
                )),
                'minView' => 2, // month view
                'maxView' => 4, // decade view
            ))
        );

        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            0,
            'ite_bootstrap_datetimepicker'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type'] = 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'date';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_bootstrap_datetimepicker_date';
    }
}