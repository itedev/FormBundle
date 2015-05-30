<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapDaterangepicker;

use ITE\FormBundle\Form\DataTransformer\RangeToStringTransformer;
use ITE\FormBundle\Form\Type\Plugin\AbstractPluginType;
use ITE\FormBundle\SF\Plugin\BootstrapDaterangepickerPlugin;
use ITE\FormBundle\Util\MomentJsUtils;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DateTimeRangeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DateTimeRangeType extends AbstractPluginType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dateFormat = \IntlDateFormatter::MEDIUM;
        $timeFormat = \IntlDateFormatter::MEDIUM;
        $calendar = \IntlDateFormatter::GREGORIAN;
        $pattern = is_string($options['format']) ? $options['format'] : null;

        $separator = isset($options['plugin_options']['separator'])
            ? $options['plugin_options']['separator']
            : ' - ';

        $partTransformer = new DateTimeToLocalizedStringTransformer(
            $options['model_timezone'],
            $options['view_timezone'],
            $dateFormat,
            $timeFormat,
            $calendar,
            $pattern
        );
        $builder->addViewTransformer(new RangeToStringTransformer($options['class'], $separator, $partTransformer));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['plugins'][BootstrapDaterangepickerPlugin::getName()] = [
            'extras' => (object) [],
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'], [
                'format' => MomentJsUtils::icuToMomentJs($options['format']),
                'timePicker' => true,
            ])
        ];

        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            0,
            'ite_bootstrap_daterangepicker'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'model_timezone' => null,
            'view_timezone' => null,
            'format' => 'yyyy-MM-dd HH:mm:ss',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_simple_range';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_bootstrap_daterangepicker_datetime_range';
    }
}