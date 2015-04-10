<?php

namespace ITE\FormBundle\Util;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * Class FormUtils
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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
     * @param FormView $rootView
     * @param $propertyPath
     * @return null|FormView
     */
    public static function getViewByFullName(FormView $rootView, $propertyPath)
    {
        $propertyPathElements = array_map(function($value) {
            return trim($value, '[]');
        }, explode('[', $propertyPath));
        array_shift($propertyPathElements);

        $view = $rootView;
        foreach ($propertyPathElements as $propertyPathElement) {
            if (!isset($view->children[$propertyPathElement])) {
                return null;
            }
            $view = $view->children[$propertyPathElement];
        }

        return $view;
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
        } elseif (in_array('repeated', $view->vars['block_prefixes']) && 0 !== count($view->children)) {
            $firstChild = reset($view->children);
            $selector = '#' . $firstChild->vars['id'];
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

    /**
     * @param FormInterface $form
     * @param $type
     * @return bool
     */
    public static function isFormTypeChildOf(FormInterface $form, $type)
    {
        return self::isResolvedFormTypeChildOf($form->getConfig()->getType(), $type);
    }

    /**
     * @param FormView $view
     * @param $type
     * @return bool
     */
    public static function isFormViewContainBlockPrefix(FormView $view, $type)
    {
        return in_array($type, $view->vars['block_prefixes']);
    }

    /**
     * @param FormInterface $form
     * @return string
     */
    public static function getErrorsAsString(FormInterface $form)
    {
        $errors = '';
        foreach ($form->getErrors() as $error) {
            $errors .= $error->getMessage() . "\n";
        }

        foreach ($form as $child) {
            if ($child instanceof FormInterface && $error = self::getErrorsAsString($child)) {
                $errors .= $error;
            }
        }

        return $errors;
    }

    /**
     * @param FormInterface $form
     * @param $plugin
     * @return bool
     */
    public static function isFormHasPlugin(FormInterface $form, $plugin)
    {
        $options = $form->getConfig()->getOptions();
        if (!isset($options['plugins']) || !isset($options['plugins'][$plugin])) {
            return false;
        }

        return true;
    }

    /**
     * @param $text
     * @return string
     */
    public static function humanize($text)
    {
        return ucfirst(trim(strtolower(preg_replace(array('/([A-Z])/', '/[_\s]+/'), array('_$1', ' '), $text))));
    }

    /**
     * @param FormInterface $form
     * @param $callback
     * @throws \InvalidArgumentException
     */
    public static function formWalkRecursive(FormInterface $form, $callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException();
        }
        foreach ($form as $child) {
            /** @var $child FormInterface */
            call_user_func($callback, $child);
            if ($child->count()) {
                self::formWalkRecursive($child, $callback);
            }
        }
    }
} 