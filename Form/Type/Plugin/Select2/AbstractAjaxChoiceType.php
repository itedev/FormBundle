<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\Form\DataTransformer\StringToArrayTransformer;
use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
        if ($options['multiple']) {
            $builder->addViewTransformer(new StringToArrayTransformer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['multiple']) {
            $view->vars['full_name'] = substr($view->vars['full_name'], 0, -2);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'empty_data' => '',
        ]);
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
