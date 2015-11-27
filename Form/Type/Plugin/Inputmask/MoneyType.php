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
 * Class MoneyType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MoneyType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $pluginOptions = [
            'digits' => $options['precision'],
        ];
        if ($options['grouping']) {
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
                        'autoUnmask' => true,
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
        return 'money';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_inputmask_money';
    }
}