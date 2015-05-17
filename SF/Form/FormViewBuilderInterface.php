<?php

namespace ITE\FormBundle\SF\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use ITE\FormBundle\SF\Form\FormView as ClientFormView;

/**
 * Interface FormViewBuilderInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormViewBuilderInterface
{
    /**
     * @param FormView $view
     * @param FormInterface $form
     * @return ClientFormView
     */
    public function createView(FormView $view, FormInterface $form);
}