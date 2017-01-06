<?php

namespace ITE\FormBundle\FormAccess;

use ITE\FormBundle\Form\FormInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use Symfony\Component\Form\FormView;

/**
 * Interface FormAccessorInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormAccessorInterface
{
    /**
     * @param FormInterface $form
     * @param string|FormPathInterface $formPath
     * @return FormInterface
     */
    public function getForm(FormInterface $form, $formPath);

    /**
     * @param FormView $view
     * @param string|FormPathInterface $formPath
     * @return FormView
     */
    public function getView(FormView $view, $formPath);

    /**
     * @param ClientFormView $clientView
     * @param string|FormPathInterface $formPath
     * @return ClientFormView|null
     */
    public function getClientView(ClientFormView $clientView, $formPath);
}
