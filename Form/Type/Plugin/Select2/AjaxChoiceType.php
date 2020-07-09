<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\Form\ChoiceList\AjaxChoiceList;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Util\FormUtil;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AjaxChoiceType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxChoiceType extends AbstractAjaxChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ('choice' !== $options['widget']) {
            return;
        }

        foreach ([FormEvents::PRE_SET_DATA, FormEvents::PRE_SUBMIT] as $eventName) {
            $builder->addEventListener($eventName, function (FormEvent $event) use ($options) {
                $data = $event->getData();
                /** @var AjaxChoiceList $choiceList */
                $choiceList = $options['choice_list'];

                if (!FormUtil::isEmpty($data)) {
                    $choiceList->addDataChoices($data);
                }
            }, -255);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->addPlugin(Select2Plugin::getName(), [
            'extras' => [
                'ajax' => true,
                'allow_create' => $options['allow_create'],
                'create_option_format' => $options['create_option_format'],
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
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'allow_create' => false,
            'create_option_format' => null, // available placeholders: %term%
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
        return 'ite_ajax_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_select2_ajax_choice';
    }
}
