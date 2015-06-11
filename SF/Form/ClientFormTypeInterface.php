<?php

namespace ITE\FormBundle\SF\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Interface ClientFormTypeInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface ClientFormTypeInterface
{
    /**
     * @param ClientFormView $clientView
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options);

    /**
     * @return string|null|ClientFormTypeInterface
     */
    public function getParent();

    /**
     * @return string The name of this type
     */
    public function getName();
}