<?php

namespace ITE\FormBundle\FormAccess;

use Symfony\Component\Form\FormInterface;
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
}