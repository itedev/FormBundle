<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\SF\Form\ClientFormTypeExtensionInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Form\ClientFormViewBuilderInterface;
use ITE\FormBundle\SF\SFFormExtensionInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use ITE\FormBundle\Form\FormInterface as ExtendedFormInterface;

/**
 * Class FormTypeFormViewExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeFormViewExtension extends AbstractTypeExtension implements ClientFormTypeExtensionInterface
{
    /**
     * @var SFFormExtensionInterface
     */
    protected $sfForm;

    /**
     * @var ClientFormViewBuilderInterface
     */
    protected $builder;

    /**
     * @param SFFormExtensionInterface $sfForm
     * @param ClientFormViewBuilderInterface $builder
     */
    public function __construct(SFFormExtensionInterface $sfForm, ClientFormViewBuilderInterface $builder)
    {
        $this->sfForm = $sfForm;
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!$form->isRoot() || null !== $view->parent) {
            return;
        }

        $id = isset($view->vars['attr']['id']) ? $view->vars['attr']['id'] : $view->vars['id'];
        $newId = $id . '_form';

        $view->vars['attr']['id'] = $newId;
        $view->vars['id'] = $newId;

        /** @var ExtendedFormInterface $form */
        $clientView = $this->builder->createClientView($view, $form);
        $this->sfForm->getFormBag()->add($form->getName(), $clientView);
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->addOptions([
            'read_only' => $view->vars['read_only'],
            'compound' => $view->vars['compound'],
            'required' => $view->vars['required'],
            'errors' => iterator_to_array($view->vars['errors']),
            'valid' => $view->vars['valid'],
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
