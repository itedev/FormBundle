<?php

namespace ITE\FormBundle\Util;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * Class FormUtils
 * @package ITE\FormBundle\Util
 */
class FormUtils
{
    /**
     * @param FormView $view
     * @return FormView
     */
    public static function getRootView(FormView $view)
    {
        $root = $view;
        while (null !== $root->parent) {
            $root = $root->parent;
        }

        return $root;
    }

    /**
     * @param FormInterface $form
     * @return string
     */
    public static function getFullName(FormInterface $form)
    {
        $fullName = '';
        for ($type = $form; null !== $type; $type = $type->getParent()) {
            $fullName = (!$type->isRoot() ? '[' : '')
                . $type->getName()
                . (!$type->isRoot() ? ']' : '')
                . $fullName;
        }

        return $fullName;
    }

    /**
     * @param FormView $view
     * @return string
     */
    public static function generateSelector(FormView $view)
    {
        $selector = '#' . $view->vars['id'];
        if (isset($view->vars['expanded']) && $view->vars['expanded']) {
            $selector .= sprintf(' input[type="%s"]', isset($view->vars['multiple']) && $view->vars['multiple']
                ? 'checkbox'
                : 'radio');
        }

        return $selector;
    }

    /**
     * @param ResolvedFormTypeInterface $resolvedFormType
     * @param $type
     * @return bool
     */
    public static function isResolvedFormTypeChildOf(ResolvedFormTypeInterface $resolvedFormType, $type)
    {
        $root = $resolvedFormType;
        while (null !== $root->getParent()) {
            if ($type === $root->getName()) {
                return true;
            }
            $root = $root->getParent();
        }

        return false;
    }

} 