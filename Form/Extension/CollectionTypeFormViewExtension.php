<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\SF\Form\ClientFormTypeExtensionInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Form\ClientFormViewBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class CollectionTypeFormViewExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CollectionTypeFormViewExtension extends AbstractTypeExtension implements ClientFormTypeExtensionInterface
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
        if (!array_key_exists('prototype', $view->vars)) {
            return;
        }

        $prototypeForm = $form->getConfig()->getAttribute('prototype');
        $prototypeView = $view->vars['prototype'];

        $prototypeClientView = $this->builder->createClientView($prototypeView, $prototypeForm, $clientView);

        $clientView->setOption('prototype_view', $prototypeClientView);
        $clientView->setOption('prototype_name', $options['prototype_name']);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'collection';
    }

}