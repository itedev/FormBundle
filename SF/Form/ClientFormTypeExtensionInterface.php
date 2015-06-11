<?php

namespace ITE\FormBundle\SF\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Interface ClientFormTypeExtensionInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface ClientFormTypeExtensionInterface
{
    /**
     * @param ClientFormView $clientView
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options);

    /**
     * @return string
     */
    public function getExtendedType();
}