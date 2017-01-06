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
 * Class IntegerType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class IntegerType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $pluginOptions = [];
        if (!$options['grouping']) {
            $pluginOptions['autoUnmask'] = true;
        } else {
            $pluginOptions['autoGroup'] = true;
            $pluginOptions['groupSize'] = LocaleUtils::getGroupingSize();
            $pluginOptions['groupSeparator'] = LocaleUtils::getGroupingSeparatorSymbol();
        }

        $clientView->setOption('plugins', [
            InputmaskPlugin::getName() => [
                'extras' => (object) [],
                'options' => (object) array_replace_recursive(
                    [
                        'alias' => 'integer',
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
        return 'integer';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_inputmask_integer';
    }
}
