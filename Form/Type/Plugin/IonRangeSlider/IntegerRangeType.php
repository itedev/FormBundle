<?php

namespace ITE\FormBundle\Form\Type\Plugin\IonRangeSlider;

use ITE\FormBundle\Form\DataTransformer\RangeToStringTransformer;
use ITE\FormBundle\Form\Type\Plugin\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\IonRangeSliderPlugin;
use Symfony\Component\Form\Extension\Core\DataTransformer\IntegerToLocalizedStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class IntegerRangeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class IntegerRangeType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $partTransformer = new IntegerToLocalizedStringTransformer(
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
        $clientView->setOption('plugins', [
            IonRangeSliderPlugin::getName() => [
                'extras' => (object) [],
                'options' => array_replace_recursive($this->options, $options['plugin_options'], [
                    'type' => 'double',
                ]),
            ],
        ]);
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
            'rounding_mode' => IntegerToLocalizedStringTransformer::ROUND_DOWN,
        ]);

        $resolver->setAllowedValues([
            'rounding_mode' => [
                IntegerToLocalizedStringTransformer::ROUND_FLOOR,
                IntegerToLocalizedStringTransformer::ROUND_DOWN,
                IntegerToLocalizedStringTransformer::ROUND_HALF_DOWN,
                IntegerToLocalizedStringTransformer::ROUND_HALF_EVEN,
                IntegerToLocalizedStringTransformer::ROUND_HALF_UP,
                IntegerToLocalizedStringTransformer::ROUND_UP,
                IntegerToLocalizedStringTransformer::ROUND_CEILING,
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
        return 'ite_ion_range_slider_integer_range';
    }
}