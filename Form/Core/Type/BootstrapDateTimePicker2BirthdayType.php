<?php

namespace ITE\FormBundle\Form\Core\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class BootstrapDateTimePicker2BirthdayType
 * @package ITE\FormBundle\Form\Core\Type
 */
class BootstrapDateTimePicker2BirthdayType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'years' => range(date('Y') - 120, date('Y')),
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

        $view->vars['element_data']['options'] = array_replace_recursive($view->vars['element_data']['options'], array(
            'viewMode' => 2, //years
            'startDate' => $dateTimeToLocalizedStringTransformer->transform($startDate),
            'endDate' => $dateTimeToLocalizedStringTransformer->transform($endDate),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_bootstrap_datetimepicker2_date';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_bootstrap_datetimepicker2_birthday';
    }
}