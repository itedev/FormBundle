<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\SF\Form\ClientFormTypeExtensionInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Form\ClientFormViewBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class ChoiceTypeFormViewExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ChoiceTypeFormViewExtension extends AbstractTypeExtension implements ClientFormTypeExtensionInterface
{
    /**
     * @var ClientFormViewBuilderInterface
     */
    protected $builder;

    /**
     * @param ClientFormViewBuilderInterface $builder
     */
    public function __construct(ClientFormViewBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        if (!$options['expanded']) {
            return;
        }

        $clientView->setOption('delegate_selector', $options['multiple'] ? ':checkbox' : ':radio');
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'choice';
    }

}