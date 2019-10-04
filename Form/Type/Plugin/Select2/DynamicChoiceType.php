<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DynamicChoiceType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DynamicChoiceType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->addPlugin(Select2Plugin::getName(), [
            'extras' => [
                'dynamic' => true,
                'domain' => $options['domain'],
                'preload_choices' => $options['preload_choices'],
                'allow_create' => $options['allow_create'],
                'create_option_format' => $options['create_option_format'],
            ],
            'options' => array_replace_recursive($this->options, $options['plugin_options'], [
                'multiple' => $options['multiple'],
                'placeholder' => $options['placeholder'],
                'allowClear' => !$options['required'],
                'minimumResultsForSearch' => 0,
            ]),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'allow_create' => true,
            'create_option_format' => '%term% (Create New)', // available placeholders: %term%
        ]);
        $resolver->setAllowedTypes([
            'allow_create' => ['bool'],
            'create_option_format' => ['null', 'string'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_dynamic_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_select2_dynamic_choice';
    }
}
