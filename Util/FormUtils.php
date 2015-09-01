<?php

namespace ITE\FormBundle\Util;

use ITE\Common\Util\ReflectionUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Component\Form\FormBuilderInterface;
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
    public static function getViewRoot(FormView $view)
    {
        $root = $view;
        while (null !== $root->parent) {
            $root = $root->parent;
        }

        return $root;
    }

    /**
     * @param FormView $view
     * @return bool
     */
    public static function isViewRoot(FormView $view)
    {
        return null === $view->parent;
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
     * @param FormInterface $form
     * @param string $fullName
     * @return null|FormInterface
     */
    public static function getFormByFullName(FormInterface $form, $fullName)
    {
        $names = array_map(function($name) {
            return trim($name, '[]');
        }, explode('[', $fullName));
        array_shift($names);

        $current = $form;
        foreach ($names as $name) {
            if (!$current->has($name)) {
                return null;
            }
            $current = $current->get($name);
        }

        return $current;
    }

    /**
     * @param FormView $view
     * @param string $fullName
     * @return null|FormView
     */
    public static function getViewByFullName(FormView $view, $fullName)
    {
        $names = array_map(function($name) {
            return trim($name, '[]');
        }, explode('[', $fullName));
        array_shift($names);

        $current = $view;
        foreach ($names as $name) {
            if (!isset($current->children[$name])) {
                return null;
            }
            $current = $current->children[$name];
        }

        return $current;
    }

    /**
     * @param FormView $view
     * @return string
     *
     * @deprecated
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
     * @param callable $callback
     * @param mixed|null $data
     * @throws \InvalidArgumentException
     */
    public static function formWalkRecursive(FormInterface $form, $callback, $data = null)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException();
        }
        foreach ($form as $child) {
            /** @var $child FormInterface */
            $data = !is_array($data) ? [$data] : $data;
            $result = call_user_func_array($callback, array_merge([$child], $data));
            if ($child->count()) {
                self::formWalkRecursive($child, $callback, $result);
            }
        }
    }

    /**
     * @param FormInterface $form
     * @param $callback
     * @param null $data
     */
    public static function formWalkRecursiveWithPrototype(FormInterface $form, $callback, $data = null)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException();
        }
        foreach ($form as $child) {
            /** @var $child FormInterface */
            $data = !is_array($data) ? [$data] : $data;
            $result = call_user_func_array($callback, array_merge([$child], $data));

            $type = $child->getConfig()->getType();
            $isCollection = FormUtils::isResolvedFormTypeChildOf($type, 'collection');
            $prototype = $child->getConfig()->getAttribute('prototype');
            if ($isCollection && null !== $prototype) {
                $result = !is_array($result) ? [$result] : $result;
                $result = call_user_func_array($callback, array_merge([$prototype], $result));
                self::formWalkRecursiveWithPrototype($prototype, $callback, $result);
            } else {
                if ($child->count()) {
                    self::formWalkRecursiveWithPrototype($child, $callback, $result);
                }
            }
        }
    }

    /**
     * @param FormView $view
     * @param $callback
     * @param mixed|null $data
     * @throws \InvalidArgumentException
     */
    public static function formViewWalkRecursive(FormView $view, $callback, $data = null)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException();
        }
        foreach ($view as $child) {
            /** @var $child FormView */
            $data = !is_array($data) ? [$data] : $data;
            $result = call_user_func_array($callback, array_merge([$child], $data));
            if ($child->count()) {
                self::formViewWalkRecursive($child, $callback, $result);
            }
        }
    }

    /**
     * @param FormView $view
     * @param $callback
     * @param mixed|null $data
     * @throws \InvalidArgumentException
     */
    public static function formViewWalkRecursiveWithPrototype(FormView $view, $callback, $data = null)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException();
        }
        foreach ($view as $child) {
            /** @var $child FormView */
            $data = !is_array($data) ? [$data] : $data;
            $result = call_user_func_array($callback, array_merge([$child], $data));

            if (array_key_exists('prototype', $child->vars)) {
                $prototype = $child->vars['prototype'];

                $result = !is_array($result) ? [$result] : $result;
                $result = call_user_func_array($callback, array_merge([$prototype], $result));
                self::formViewWalkRecursiveWithPrototype($prototype, $callback, $result);
            } else {
                if ($child->count()) {
                    self::formViewWalkRecursiveWithPrototype($child, $callback, $result);
                }
            }
        }
    }

    /**
     * @param FormInterface $form
     * @param EventSubscriberInterface $subscriber
     */
    public static function addEventSubscriber(FormInterface $form, $subscriber)
    {
        $ed = $form->getConfig()->getEventDispatcher();
        $rawEd = EventDispatcherUtils::getRawEventDispatcher($ed);

        $rawEd->addSubscriber($subscriber);
    }

    /**
     * @param FormInterface $form
     * @param string $eventName
     * @param callable $listener
     * @param int $priority
     */
    public static function addEventListener(FormInterface $form, $eventName, $listener, $priority = 0)
    {
        $ed = $form->getConfig()->getEventDispatcher();
        $rawEd = EventDispatcherUtils::getRawEventDispatcher($ed);

        $rawEd->addListener($eventName, $listener, $priority);
    }

    /**
     * @param FormBuilderInterface $parent
     * @param string $child
     * @param bool|false $referenceLevelUp
     * @return mixed
     */
    public static function getBuilderReference(FormBuilderInterface $parent, $child, &$referenceLevelUp = false)
    {
//        $children = array_keys($parent->all());
        $children = array_keys(ReflectionUtils::getValue($parent, 'children'));
        $index = array_search($child, $children);
        if (0 !== $index) {
            // this is not first child in parent form - so take previous sibling as reference point
            $reference = $parent->get($children[$index - 1]);
            $referenceLevelUp = false;
        } else {
            // this is first child in parent form - so take parent as reference point
            $reference = $parent;
            $referenceLevelUp = true;
        }

        return $reference;
    }

    /**
     * @param FormInterface $parent
     * @param string $child
     * @param bool|false $referenceLevelUp
     * @return FormInterface
     */
    public static function getFormReference(FormInterface $parent, $child, &$referenceLevelUp = false)
    {
        $children = array_keys($parent->all());
        $index = array_search($child, $children);
        if (0 !== $index) {
            // this is not first child in parent form - so take previous sibling as reference point
            $reference = $parent->get($children[$index - 1]);
            $referenceLevelUp = false;
        } else {
            // this is first child in parent form - so take parent as reference point
            $reference = $parent;
            $referenceLevelUp = true;
        }

        return $reference;
    }

    /**
     * @param FormInterface $form
     * @param mixed $data
     */
    public static function setData(FormInterface $form, $data)
    {
        if ($data === $form->getData()) {
            return;
        }

        $formFactory = $form->getConfig()->getFormFactory();
        $name = $form->getConfig()->getName();
        $type = $form->getConfig()->getType();
        $options = $form->getConfig()->getOptions();

        if (isset($options['data'])) {
            unset($options['data']);
        }
        if (isset($options['hierarchical_data'])) {
            unset($options['hierarchical_data']);
        }

        $newForm = $formFactory->createNamed($name, $type, $data, $options);
        $newForm->initialize();

        $submitted = $form->isSubmitted();

        $modelData = ReflectionUtils::getValue($newForm, 'modelData');
        $normData = ReflectionUtils::getValue($newForm, 'normData');
        $viewData = ReflectionUtils::getValue($newForm, 'viewData');
        $children = ReflectionUtils::getValue($newForm, 'children');

        foreach ($children as $child) {
            ReflectionUtils::setValue($child, 'parent', $form);
            ReflectionUtils::setValue($child, 'submitted', $submitted);
        }
        ReflectionUtils::setValue($form, 'modelData', $modelData);
        ReflectionUtils::setValue($form, 'normData', $normData);
        ReflectionUtils::setValue($form, 'viewData', $viewData);
        ReflectionUtils::setValue($form, 'children', $children);
    }

    /**
     * @param FormInterface $form
     * @param EventDispatcherInterface $ed
     */
    public static function setEventDispatcher(FormInterface $form, EventDispatcherInterface $ed)
    {
        $config = $form->getConfig();

        $refClass = new \ReflectionClass($config);
        while (!$refClass->hasProperty('dispatcher')) {
            $refClass = $refClass->getParentClass();
        }
        $refProp = $refClass->getProperty('dispatcher');
        $refProp->setAccessible(true);
        $refProp->setValue($config, $ed);
        $refProp->setAccessible(false);
    }
} 