<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapDatetimepicker;

use ITE\FormBundle\Form\Type\Plugin\AbstractPluginType;
use ITE\FormBundle\SF\Plugin\BootstrapDatetimepickerPlugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TimeType
 *
 * Time type wrapper for bootstrap-datetimepeeker
 * Plugin URL: https://github.com/Eonasdan/bootstrap-datetimepicker
 *
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TimeType extends AbstractPluginType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'widget' => 'single_text',
            'plugin_options' => [
                'locale' => \Locale::getDefault()
            ],
        ]);
        $resolver->setAllowedValues([
            'widget' => ['single_text'],
        ]);
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

        $view->vars['plugins'][BootstrapDatetimepickerPlugin::getName()] = [
            'extras' => (object) [],
            'options' => array_replace_recursive($this->options, $options['plugin_options'], [
                'format' => BootstrapDatetimepickerPlugin::formatPHPDateTimeFormat($format),
            ])
        ];

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