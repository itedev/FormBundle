<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapClockpicker;

use ITE\Common\Util\ArrayUtils;
use ITE\FormBundle\Form\DataTransformer\LowerToUpperCaseTransformer;
use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\BootstrapClockpickerPlugin;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TimeType
 *
 * Time type wrapper for bootstrap-clockpicker
 * Plugin URL: https://github.com/weareoutman/clockpicker
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TimeType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new LowerToUpperCaseTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            0,
            'ite_bootstrap_clockpicker'
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
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->setOption('plugins', [
            BootstrapClockpickerPlugin::getName() => [
                'extras' => (object) [],
                'options' => array_replace_recursive($this->options, $options['plugin_options'], [
                    'twelvehour' => ArrayUtils::getValue($options, 'twelve_hour', false),
                ]),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'widget' => 'single_text',
            'with_seconds' => false,
        ]);
        $resolver->setAllowedValues([
            'widget' => ['single_text'],
            'with_seconds' => [false],
        ]);
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
        return 'ite_bootstrap_clockpicker_time';
    }
}
