<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\Form\Extension\AjaxToken\AjaxTokenProviderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AjaxTokenFormTypeExtension
 * @package ITE\FormBundle\Form\Extension
 */
class AjaxTokenFormTypeExtension extends AbstractTypeExtension
{
    /**
     * @var AjaxTokenProviderInterface $ajaxTokenProvider
     */
    private $ajaxTokenProvider;

    /**
     * @param AjaxTokenProviderInterface $ajaxTokenProvider
     */
    public function __construct(AjaxTokenProviderInterface $ajaxTokenProvider)
    {
        $this->ajaxTokenProvider = $ajaxTokenProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'ajax_token' => false,
            'ajax_token_field_name' => '_ajax_token',
        ));
        $resolver->setAllowedTypes(array(
            'ajax_token' => 'bool',
            'ajax_token_field_name' => 'string',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['ajax_token']) {
            return;
        }

        $builder->setAttribute('ajax_token_factory', $builder->getFormFactory());

        $fullFieldName = sprintf('%s[%s]', $builder->getName(), $options['ajax_token_field_name']);
        $builder->setAttribute('ajax_token_value', $this->ajaxTokenProvider->generateAjaxToken($fullFieldName));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!$options['ajax_token']) {
            return;
        }

        if ($form->isRoot()) {
            $view->vars['ajax_token'] = $options['ajax_token'];
            $view->vars['ajax_token_field_name'] = $options['ajax_token_field_name'];
            $view->vars['ajax_token_value'] = $form->getConfig()->getAttribute('ajax_token_value');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['ajax_token'] && !$view->parent && $options['compound']) {
            $factory = $form->getConfig()->getAttribute('ajax_token_factory');
            $data = $form->getConfig()->getAttribute('ajax_token_value');

            $ajaxTokenForm = $factory->createNamed($options['ajax_token_field_name'], 'hidden', $data, array(
                    'mapped' => false,
                ));

            $view->children[$options['ajax_token_field_name']] = $ajaxTokenForm->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
} 