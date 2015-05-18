<?php

namespace ITE\FormBundle\SF\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView as ServerFormView;
use ITE\FormBundle\SF\Form\FormView as ClientFormView;

/**
 * Interface FormViewBuilderInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormViewBuilderInterface
{
    /**
     * @param ServerFormView $view
     * @param FormInterface $form
     * @return ClientFormView
     */
    public function createView(ServerFormView $view, FormInterface $form);
}