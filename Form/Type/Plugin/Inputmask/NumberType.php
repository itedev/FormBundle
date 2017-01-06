<?php

namespace ITE\FormBundle\Form\Type\Plugin\Inputmask;

use ITE\Common\Util\LocaleUtils;
use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\InputmaskPlugin;
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
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $pluginOptions = [
            'digits' => LocaleUtils::getPrecision($options['precision']),
        ];
        if (!$options['grouping']) {
            $pluginOptions['autoUnmask'] = true;
        } else {
            $pluginOptions['autoGroup'] = true;
            $pluginOptions['groupSize'] = LocaleUtils::getGroupingSize();
            $pluginOptions['groupSeparator'] = LocaleUtils::getGroupingSeparatorSymbol();
            $pluginOptions['radixPoint'] = LocaleUtils::getDecimalSeparatorSymbol();
        }

        $clientView->setOption('plugins', [
            InputmaskPlugin::getName() => [
                'extras' => (object) [],
                'options' => (object) array_replace_recursive(
                    [
                        'alias' => 'decimal',
                        'digitsOptional' => true,
                        'rightAlign' => false,
                        'allowPlus' => false,
                    ],
                    $this->options,
                    $options['plugin_options'],
                    $pluginOptions
                ),
            ],
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
        return 'ite_inputmask_number';
    }
}
