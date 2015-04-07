<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapDatetimepicker;

use ITE\FormBundle\SF\Plugin\BootstrapDatetimepickerPlugin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DateTimeType
 *
 * DateTime type wrapper for bootstrap-datetimepeeker
 * Plugin URL: https://github.com/Eonasdan/bootstrap-datetimepicker
 *
 * @package ITE\FormBundle\Form\Type\Plugin\BootstrapDatetimepicker
 */
class DateTimeType extends AbstractType
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
        $format = function(Options $options) {
            return 'yyyy-MM-dd HH' . ($options['with_minutes'] ? ':mm' : '') . ($options['with_seconds'] ? ':ss' : '');
        };

        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'format' => $format,
            'plugin_options' => array(
                'locale' => \Locale::getDefault()
            ),
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
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
        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = array();
        }
        $view->vars['plugins'][BootstrapDatetimepickerPlugin::getName()] = array(
            'extras' => (object) array(),
            'options' => array_replace_recursive($this->options, $options['plugin_options'], array(
                'format' => BootstrapDatetimepickerPlugin::formatPHPDateTimeFormat($options['format']),
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
        return 'datetime';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_bootstrap_datetimepicker_datetime';
    }
}