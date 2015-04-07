<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapDatetimepicker;

use ITE\FormBundle\SF\Plugin\BootstrapDatetimepickerPlugin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BirthdayType
 *
 * Birthday type wrapper for bootstrap-datetimepeeker
 * Plugin URL: https://github.com/Eonasdan/bootstrap-datetimepicker
 *
 * @package ITE\FormBundle\Form\Type\Plugin\BootstrapDatetimepicker
 */
class BirthdayType extends AbstractType
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
                'years' => range(date('Y') - 120, date('Y')),
                'plugin_options' => array(
                    'locale' => \Locale::getDefault()
                ),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', sprintf('%d-01-01 00:00:00', $options['years'][0]));
        $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', sprintf('%d-12-31 23:59:59', $options['years'][count($options['years']) - 1]));

        $viewTransformers = $form->getConfig()->getViewTransformers();
        /** @var $dateTimeToLocalizedStringTransformer DateTimeToLocalizedStringTransformer */
        $dateTimeToLocalizedStringTransformer = $viewTransformers[0];

        $view->vars['plugins'][BootstrapDatetimepickerPlugin::getName()]['options'] = array_replace_recursive(
            $view->vars['plugins'][BootstrapDatetimepickerPlugin::getName()]['options'], array(
                'viewMode' => 'days', // days view
                'minDate' => $dateTimeToLocalizedStringTransformer->transform($startDate),
                'maxDate' => $dateTimeToLocalizedStringTransformer->transform($endDate),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_bootstrap_datetimepicker_date';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_bootstrap_datetimepicker_birthday';
    }
}