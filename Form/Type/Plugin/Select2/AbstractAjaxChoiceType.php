<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isMultiple = $builder->getOption('multiple');

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($isMultiple) {
            $data = $event->getData();

            // Fix for select2 multiple, input value explode required
            if ($isMultiple) {
                $event->setData(null);
                if (
                    $data
                    && isset($data[0])
                    && $data[0]
                ) {
                    $event->setData(explode(",", $data[0]));
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->setOption('plugins', [
            Select2Plugin::getName() => [
                'extras' => [
                    'ajax' => true,
                ],
                'options' => array_replace_recursive($this->options, $options['plugin_options'], [
                    'ajax' => [
                        'url' => $options['url'],
                        'dataType' => 'json',
                    ],
                    'multiple' => $options['multiple'],
                    'placeholder' => $options['placeholder'],
                    'allowClear' => !$options['required'],
                ]),
            ],
        ]);
    }
}
