<?php

namespace ITE\FormBundle\FormAccess;

use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class FormAccessor
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormAccessor implements FormAccessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function getForm(FormInterface $form, $formPath)
    {
        if (!$formPath instanceof FormPathInterface) {
            $formPath = new FormPath($formPath);
        }

        $current = $formPath->isAbsolute() ? $form->getRoot() : $form;

        $length = $formPath->getLength();
        for ($i = 0; $i < $length; $i++) {
            $isParent = $formPath->isParent($i);

            if ($isParent) {
                if ($current->isRoot()) {
                    // error
                    return null;
                }

                $current = $current->getParent();
            } else {
                $element = $formPath->getElement($i);
                if (!$current->has($element)) {
                    // error
                    return null;
                }

                $current = $current->get($element);
            }
        }

        return $current;
    }

    /**
     * {@inheritdoc}
     */
    public function getView(FormView $view, $formPath)
    {
        if (!$formPath instanceof FormPathInterface) {
            $formPath = new FormPath($formPath);
        }

        $current = $formPath->isAbsolute() ? FormUtils::getViewRoot($view) : $view;

        $length = $formPath->getLength();
        for ($i = 0; $i < $length; $i++) {
            $isParent = $formPath->isParent($i);

            if ($isParent) {
                if (FormUtils::isViewRoot($current)) {
                    // error
                    return null;
                }

                $current = $current->parent;
            } else {
                $element = $formPath->getElement($i);
                if (!isset($current[$element])) {
                    // error
                    return null;
                }

                $current = $current[$element];
            }
        }

        return $current;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientView(ClientFormView $clientView, $formPath)
    {
        if (!$formPath instanceof FormPathInterface) {
            $formPath = new FormPath($formPath);
        }

        $current = $formPath->isAbsolute() ? $clientView->getRoot() : $clientView;

        $length = $formPath->getLength();
        for ($i = 0; $i < $length; $i++) {
            $isParent = $formPath->isParent($i);

            if ($isParent) {
                if ($current->isRoot()) {
                    // error
                    return null;
                }

                $current = $current->getParent();
            } else {
                $element = $formPath->getElement($i);
                if (!$current->hasChild($element)) {
                    // error
                    return null;
                }

                $current = $current->getChild($element);
            }
        }

        return $current;
    }
}