<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapSpinedit;

use ITE\FormBundle\Form\Type\Plugin\AbstractPluginType;
use ITE\FormBundle\SF\Plugin\BootstrapSpineditPlugin;
use ITE\Common\Util\LocaleUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class NumberType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class NumberType extends AbstractPluginType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['plugins'][BootstrapSpineditPlugin::getName()] = [
            'extras' => (object) [],
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'], [
                'numberOfDecimals' => LocaleUtils::getPrecision($options['precision']),
            ])
        ];
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
        return 'ite_bootstrap_spinedit_number';
    }
}