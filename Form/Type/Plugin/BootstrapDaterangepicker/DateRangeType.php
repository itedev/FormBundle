<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapDaterangepicker;

use ITE\FormBundle\Form\DataTransformer\RangeToStringTransformer;
use ITE\FormBundle\Form\Type\Plugin\Core\AbstractDatePluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\BootstrapDaterangepickerPlugin;
use ITE\FormBundle\Util\MomentJsUtils;
use Symfony\Component\Form\Extension\Core\DataTransformer\DataTransformerChain;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DateRangeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DateRangeType extends AbstractDatePluginType implements ClientFormTypeInterface
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

        $builder->addViewTransformer(new RangeToStringTransformer($options['class'], $options['separator'], $partViewTransformer));
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
            'ite_bootstrap_daterangepicker'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        // don't call parent method!
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->addPlugin(BootstrapDaterangepickerPlugin::getName(), [
            'extras' => (object) [],
            'options' => array_replace_recursive($this->options, $options['plugin_options'], [
                'separator' => $options['separator'],
                'format' => MomentJsUtils::icuToMomentJs($options['format']),
                'timePicker' => false,
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
            'class' => 'ITE\FormBundle\Form\Data\DateRange',
            'separator' => $this->options['separator'] ?? ' - ',
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
        return 'ite_bootstrap_daterangepicker_date_range';
    }
}
