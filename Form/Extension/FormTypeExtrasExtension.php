<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeExtrasExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeExtrasExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['extra_attributes'])) {
            foreach ($options['extra_attributes'] as $name => $value) {
                $builder->setAttribute($name, $value);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (isset($options['extra_view_vars'])) {
            foreach ($options['extra_view_vars'] as $name => $value) {
                $view->vars[$name] = $value;
            }
        }
        if (isset($options['extra_block_prefixes'])) {
            $extraBlockPrefixes = (array) $options['extra_block_prefixes'];
            $blockPrefixes = $view->vars['block_prefixes'];
            array_splice($blockPrefixes, -1, 0, $extraBlockPrefixes);
            $view->vars['block_prefixes'] = $blockPrefixes;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional([
            'extra_options',
            'extra_attributes',
            'extra_view_vars',
            'extra_block_prefixes',
        ]);
        $resolver->setAllowedTypes([
            'extra_options' => ['array'],
            'extra_attributes' => ['array'],
            'extra_view_vars' => ['array'],
            'extra_block_prefixes' => ['string', 'array'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
