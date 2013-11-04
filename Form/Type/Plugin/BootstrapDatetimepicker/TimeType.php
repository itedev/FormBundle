<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapDatetimepicker;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TimeType
 * @package ITE\FormBundle\Form\Type\Plugin\BootstrapDatetimepicker
 */
class TimeType extends AbstractType
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
        $format = 'HH';
        if ($options['with_minutes']) {
            $format .= ':mm';
        }
        if ($options['with_seconds']) {
            $format .= ':ss';
        }

        $view->vars['element_data'] = array(
            'extras' => (object) $options['extras'],
            'options' => array_replace_recursive($this->options, $options['plugin_options'], array(
                'format' => strtr($format, array(
                    'a' => 'p', // am/pm marker
                    'm' => 'i', // minute in hour
                    'h' => 'H', // hour in am/pm (1~12)
                    'H' => 'h', // hour in day (0~23)
                    'MMMM' => 'MM', // month in year (September)
                    'MMM' => 'M', // month in year (Sept)
                    'MM' => 'mm', // month in year (09)
                    'M' => 'm', // month in year (9)
                )),
                'startView' => 1, // day view
                'minView' => 0, // hour view
                'maxView' => 1, // day view
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
        return 'time';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_bootstrap_datetimepicker_time';
    }
}