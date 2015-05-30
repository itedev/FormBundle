<?php

namespace ITE\FormBundle\Form\Type\Plugin\IonRangeSlider;

use ITE\FormBundle\Form\DataTransformer\RangeToStringTransformer;
use ITE\FormBundle\Form\Type\Plugin\AbstractPluginType;
use ITE\FormBundle\SF\Plugin\IonRangeSliderPlugin;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class NumberRangeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class NumberRangeType extends AbstractPluginType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $partTransformer = new NumberToLocalizedStringTransformer(
            $options['precision'],
            $options['grouping'],
            $options['rounding_mode']
        );

        $builder->addViewTransformer(new RangeToStringTransformer($options['class'], ';', $partTransformer));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['plugins'][IonRangeSliderPlugin::getName()] = [
            'extras' => (object) [],
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'], [
                'type' => 'double',
            ])
        ];

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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'precision' => null,
            'grouping' => false,
            'rounding_mode' => NumberToLocalizedStringTransformer::ROUND_HALF_UP,
        ]);

        $resolver->setAllowedValues([
            'rounding_mode' => [
                NumberToLocalizedStringTransformer::ROUND_FLOOR,
                NumberToLocalizedStringTransformer::ROUND_DOWN,
                NumberToLocalizedStringTransformer::ROUND_HALF_DOWN,
                NumberToLocalizedStringTransformer::ROUND_HALF_EVEN,
                NumberToLocalizedStringTransformer::ROUND_HALF_UP,
                NumberToLocalizedStringTransformer::ROUND_UP,
                NumberToLocalizedStringTransformer::ROUND_CEILING,
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