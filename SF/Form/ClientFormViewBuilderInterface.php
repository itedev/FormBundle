<?php

namespace ITE\FormBundle\SF\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Interface ClientFormViewBuilderInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface ClientFormViewBuilderInterface
{
    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param ClientFormView $parent
     * @return ClientFormView
     */
    public function createClientView(FormView $view, FormInterface $form, ClientFormView $parent = null);
}
