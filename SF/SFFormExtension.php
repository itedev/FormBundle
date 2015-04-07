<?php

namespace ITE\FormBundle\SF;

use ITE\JsBundle\SF\SFExtension;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class SFFormExtension
 * @package ITE\FormBundle\SF
 */
class SFFormExtension extends SFExtension implements SFFormExtensionInterface
{
    /**
     * @var array
     */
    protected $components = array();

    /**
     * @var array
     */
    protected $plugins = array();

    /**
     * @var ElementBag $elementBag
     */
    protected $elementBag;

    /**
     * @var array
     */
    protected $formErrors = array();

    /**
     *
     */
    public function __construct()
    {
        $this->elementBag = new ElementBag();
    }

    /**
     * {@inheritdoc}
     */
    public function getStylesheets()
    {
        $inputs = array();

        // add component css
        foreach ($this->getComponents() as $component) {
            /** @var $component ExtensionInterface */
            $inputs = array_merge($inputs, $component->getStylesheets());
        }

        // add plugin css
        foreach ($this->getPlugins() as $plugin) {
            /** @var $plugin ExtensionInterface */
            $inputs = array_merge($inputs, $plugin->getStylesheets());
        }

        return $inputs;
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        $inputs = array('@ITEFormBundle/Resources/public/js/sf.form.js');

        // add component js
        foreach ($this->getComponents() as $component) {
            /** @var $component ExtensionInterface */
            $inputs = array_merge($inputs, $component->getJavascripts());
        }

        // add plugin js
        foreach ($this->getPlugins() as $plugin) {
            /** @var $plugin ExtensionInterface */
            $inputs = array_merge($inputs, $plugin->getJavascripts());
        }

        return $inputs;
    }

    /**
     * @return string
     */
    public function dump()
    {
        $dump = '';
        $dump .= '(function($){$(function(){';

        if ($this->elementBag->count()) {
            $dump .= 'SF.elements.set(' . json_encode($this->elementBag->peekAll()) . ');';
            $dump .= 'SF.elements.apply();';
        }

        $dump .= '});})(jQuery);';

        return $dump;
    }

    /**
     * @param string $alias
     * @param ExtensionInterface $component
     */
    public function addComponent($alias, ExtensionInterface $component)
    {
        $this->components[$alias] = $component;
    }

    /**
     * Get components
     *
     * @return array
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @param string $alias
     * @param ExtensionInterface $plugin
     */
    public function addPlugin($alias, ExtensionInterface $plugin)
    {
        $this->plugins[$alias] = $plugin;
    }

    /**
     * Get plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Get elementBag
     *
     * @return ElementBag
     */
    public function getElementBag()
    {
        return $this->elementBag;
    }

//    /**
//     * @param FormView $form
//     */
//    protected function collectFormErrors(FormView $form)
//    {
//        $formErrors = array(
//            'form' => array(),
//            'children' => array(),
//        );
//        $this->processChildrenRecursive($formErrors, $form);
//        $this->formErrors = $formErrors;
//    }
//
//    /**
//     * @param $formErrors
//     * @param FormView $element
//     */
//    protected function processChildrenRecursive(&$formErrors, FormView $element)
//    {
//        if (count($element->vars['errors'])) {
//            $value = array(
//                'error_type' => $element->vars['error_type'],
//                'errors' => array_map(function($error) {
//                    /** @var $error FormError */
//                    return $error->getMessage();
//                }, $element->vars['errors']),
//            );
//
//            if (!isset($element->parent)) {
//                $formErrors['form'] = $value;
//            } else {
//                $formErrors['children'][$element->vars['full_name']] = $value;
//            }
//        }
//
//        foreach ($element->children as $child) {
//            $this->processChildrenRecursive($formErrors, $child);
//        }
//    }
}