<?php

namespace ITE\FormBundle\SF\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Interface FinishClientFormTypeExtensionInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FinishClientFormTypeExtensionInterface
{
    /**
     * @param ClientFormView $clientView
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options);

    /**
     * @return string
     */
    public function getExtendedType();
}
