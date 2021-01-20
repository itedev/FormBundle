<?php

namespace ITE\FormBundle\Form\Type\Plugin\Inputmask;

use ITE\Common\Util\LocaleUtils;
use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\InputmaskPlugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PercentType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class PercentType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $pluginOptions = [
            'digits' => LocaleUtils::getPrecision($options['precision']),
        ];

        $clientView->addPlugin(InputmaskPlugin::getName(), [
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
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'percent';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_inputmask_percent';
    }
}
