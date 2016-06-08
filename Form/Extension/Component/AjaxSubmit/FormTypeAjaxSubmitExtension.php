<?php

namespace ITE\FormBundle\Form\Extension\Component\AjaxSubmit;

use ITE\FormBundle\OptionsResolver\MultidimensionalOptionsResolver;
use ITE\FormBundle\SF\Form\ClientFormTypeExtensionInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeAjaxSubmitExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeAjaxSubmitExtension extends AbstractTypeExtension implements ClientFormTypeExtensionInterface
{
    /**
     * @var string $defaultSubmitter
     */
    protected $defaultSubmitter;

    /**
     * @param $defaultSubmitter
     */
    public function __construct($defaultSubmitter)
    {
        $this->defaultSubmitter = $defaultSubmitter;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
//        /** @var MultidimensionalOptionsResolver $resolver */
//        $subResolver = new OptionsResolver();
//
//        $resolver->setResolvers([
//            'submitter_options' => $subResolver,
//        ]);
        $resolver->setOptional([
            'ajax_submit',
            'submitter',
            'submitter_options',
        ]);
        $resolver->setDefaults([
            'ajax_submit_type' => 'replace',
        ]);
        $resolver->setAllowedTypes([
            'ajax_submit' => ['bool'],
            'submitter' => ['string'],
            'submitter_options' => ['array'],
        ]);
        $resolver->setAllowedValues([
            'ajax_submit_method' => [
                'replace',
                'replace',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        if (!$clientView->isRoot() || !isset($options['ajax_submit']) || !$options['ajax_submit']) {
            return;
        }

        $submitter = isset($options['submitter']) ? $options['submitter'] : $this->defaultSubmitter;
        $submitterOptions = isset($options['submitter_options']) ? $options['submitter_options'] : [];

        $clientView->addOptions([
            'ajax_submit' => $options['ajax_submit'],
            'submitter' => $submitter,
            'submitter_options' => $submitterOptions,
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