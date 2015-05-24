<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapSpinedit;

use ITE\FormBundle\Form\Type\Plugin\AbstractPluginType;
use ITE\FormBundle\SF\Plugin\BootstrapSpineditPlugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class IntegerType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class IntegerType extends AbstractPluginType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['plugins'][BootstrapSpineditPlugin::getName()] = [
            'extras' => (object) [],
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'], [
                'numberOfDecimals' => 0,
            ])
        ];
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
        return 'ite_bootstrap_spinedit_integer';
    }
}