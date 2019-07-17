<?php

namespace ITE\FormBundle\Form\Type\Plugin\IonRangeSlider;

use ITE\FormBundle\Form\DataTransformer\RangeToStringTransformer;
use ITE\FormBundle\Form\Type\Plugin\Core\AbstractMoneyPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\IonRangeSliderPlugin;
use ITE\FormBundle\Util\MoneyUtils;
use Symfony\Component\Form\Extension\Core\DataTransformer\DataTransformerChain;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class MoneyRangeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MoneyRangeType extends AbstractMoneyPluginType implements ClientFormTypeInterface
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
        parent::buildView($view, $form, $options);

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
        $parsedMoneyPattern = MoneyUtils::parseMoneyPattern($view->vars['money_pattern']);

        $predefinedOptions = [];
        if ('prefix' === $parsedMoneyPattern['position']) {
            $predefinedOptions['prefix'] = $parsedMoneyPattern['symbol'];
        } elseif ('suffix' === $parsedMoneyPattern['position']) {
            $predefinedOptions['postfix'] = $parsedMoneyPattern['symbol'];
        }
        $predefinedOptions['step'] = 1 / pow(10, $options['precision']);

        $clientView->addPlugin(IonRangeSliderPlugin::getName(), [
            'extras' => (object) [],
            'options' => array_replace_recursive(
                $this->options,
                $predefinedOptions,
                $options['plugin_options'],
                [
                    'type' => 'double',
                ]
            ),
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
        return 'ite_ion_range_slider_money_range';
    }
}
