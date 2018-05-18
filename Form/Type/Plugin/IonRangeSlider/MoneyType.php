<?php

namespace ITE\FormBundle\Form\Type\Plugin\IonRangeSlider;

use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\IonRangeSliderPlugin;
use ITE\FormBundle\Util\MoneyUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class MoneyType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MoneyType extends AbstractPluginType implements ClientFormTypeInterface
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
        $parsedMoneyPattern = MoneyUtils::parseMoneyPattern($view->vars['money_pattern']);

        $predefinedOptions = [];
        if ('prefix' === $parsedMoneyPattern['position']) {
            $predefinedOptions['prefix'] = $parsedMoneyPattern['symbol'];
        } elseif ('suffix' === $parsedMoneyPattern['position']) {
            $predefinedOptions['postfix'] = $parsedMoneyPattern['symbol'];
        }
        $predefinedOptions['step'] = 1 / pow(10, $options['precision']);

        $clientView->setOption('plugins', [
            IonRangeSliderPlugin::getName() => [
                'extras' => (object) [],
                'options' => array_replace_recursive(
                    $this->options,
                    $predefinedOptions,
                    $options['plugin_options'],
                    [
                        'type' => 'single',
                    ]
                ),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'money';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_ion_range_slider_money';
    }
}
