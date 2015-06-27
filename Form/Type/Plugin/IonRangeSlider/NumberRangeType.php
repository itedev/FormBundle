<?php

namespace ITE\FormBundle\Form\Type\Plugin\IonRangeSlider;

use ITE\FormBundle\Form\DataTransformer\RangeToStringTransformer;
use ITE\FormBundle\Form\Type\Plugin\Core\AbstractNumberPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\IonRangeSliderPlugin;
use Symfony\Component\Form\Extension\Core\DataTransformer\DataTransformerChain;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class NumberRangeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class NumberRangeType extends AbstractNumberPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $partViewTransformer = new DataTransformerChain($builder->getViewTransformers());
        $partModelTransformer = new DataTransformerChain($builder->getModelTransformers());
        $builder->resetViewTransformers();
        $builder->resetModelTransformers();

        $builder->addViewTransformer(new RangeToStringTransformer($options['class'], ';', $partViewTransformer));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            0,
            'ite_ion_range_slider'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $predefinedOptions = [];
        $predefinedOptions['step'] = 1 / pow(10, $options['precision']);

        $clientView->setOption('plugins', [
            IonRangeSliderPlugin::getName() => [
                'extras' => (object) [],
                'options' => array_replace_recursive(
                    $this->options,
                    $predefinedOptions,
                    $options['plugin_options'],
                    [
                        'type' => 'double',
                    ]
                ),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_simple_range';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_ion_range_slider_number_range';
    }
}