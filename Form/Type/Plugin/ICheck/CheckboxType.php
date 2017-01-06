<?php

namespace ITE\FormBundle\Form\Type\Plugin\ICheck;

use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\ICheckPlugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class CheckboxType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CheckboxType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->setOption('plugins', [
            ICheckPlugin::getName() => [
                'extras' => (object) [],
                'options' => (object) array_replace_recursive($this->options, $options['plugin_options'])
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'checkbox';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_icheck_checkbox';
    }
}
