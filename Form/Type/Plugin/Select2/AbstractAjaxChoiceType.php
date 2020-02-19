<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class AbstractAjaxChoiceType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractAjaxChoiceType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->addPlugin(Select2Plugin::getName(), [
            'extras' => [
                'ajax' => true,
            ],
            'options' => array_replace_recursive($this->options, [
                'allowClear' => !$options['required'],
            ], $options['plugin_options'], [
                'ajax' => [
                    'url' => $options['url'],
                    'dataType' => 'json',
                    'delay' => $options['ajax_delay'],
                ],
                'multiple' => $options['multiple'],
                'placeholder' => $options['placeholder'],
            ]),
        ]);
    }
}
