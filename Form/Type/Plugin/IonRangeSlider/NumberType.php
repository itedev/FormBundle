<?php

namespace ITE\FormBundle\Form\Type\Plugin\IonRangeSlider;

use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\IonRangeSliderPlugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class NumberType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class NumberType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            0,
            'ite_ion_range_slider'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $predefinedOptions = [];
        $predefinedOptions['step'] = 1 / pow(10, $options['precision']);

        $clientView->addPlugin(IonRangeSliderPlugin::getName(), [
            'extras' => (object) [],
            'options' => array_replace_recursive(
                $this->options,
                $predefinedOptions,
                $options['plugin_options'],
                [
                    'type' => 'single',
                ]
            ),
        ]);
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
        return 'ite_ion_range_slider_number';
    }
}
