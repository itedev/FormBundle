<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapDatetimepicker;

use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\BootstrapDatetimepickerPlugin;
use ITE\FormBundle\Util\MomentJsUtils;
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
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class BirthdayType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults([
            'years' => range(date('Y') - 120, date('Y')),
            'plugin_options' => [
                'locale' => \Locale::getDefault()
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', sprintf('%d-01-01 00:00:00', $options['years'][0]));
        $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', sprintf('%d-12-31 23:59:59', $options['years'][count($options['years']) - 1]));

        $viewTransformers = $form->getConfig()->getViewTransformers();
        /** @var $dateTimeToLocalizedStringTransformer DateTimeToLocalizedStringTransformer */
        $dateTimeToLocalizedStringTransformer = $viewTransformers[0];

        $plugins = $clientView->getOption('plugins', []);
        $pluginsOptions = $plugins[BootstrapDatetimepickerPlugin::getName()]['options'];

        $pluginsOptions['viewMode'] = 'days';
        $pluginsOptions['minDate'] = $dateTimeToLocalizedStringTransformer->transform($startDate);
        $pluginsOptions['maxDate'] = $dateTimeToLocalizedStringTransformer->transform($endDate);

        $plugins[BootstrapDatetimepickerPlugin::getName()]['options'] = $pluginsOptions;
        $clientView->setOption('plugins', $plugins);
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