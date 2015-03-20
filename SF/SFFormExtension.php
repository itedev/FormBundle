<?php

namespace ITE\FormBundle\SF;

use ITE\JsBundle\SF\SFExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
     * @var ContainerInterface
     */
    protected $container;

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
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->elementBag = new ElementBag();
    }

    /**
     * @return array
     */
    public function addJavascripts()
    {
        $inputs = array('@ITEFormBundle/Resources/public/js/sf.form.js');

        // add component js
        foreach ($this->getComponents() as $component) {
            /** @var $component ExtensionInterface */
            if ($component->isEnabled($this->container)) {
                $inputs = array_merge($inputs, $component->addJavascripts($this->container));
            }
        }

        // add plugin js
        foreach ($this->getPlugins() as $plugin) {
            /** @var $plugin ExtensionInterface */
            if ($plugin->isEnabled($this->container)) {
                $inputs = array_merge($inputs, $plugin->addJavascripts($this->container));
            }
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
     * @param GetResponseForControllerResultEvent $event
     */
    public function onAjaxRequest(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $result = $event->getControllerResult();

        // is form was submitted via ajax - get its errors if exist
        if (in_array($request->getMethod(), array('GET', 'POST'))) {
            $property = 'POST' === $request->getMethod() ? 'request' : 'query';
            if (is_array($result) || $result instanceof \Traversable) {
                foreach ($result as $var) {
                    if ($var instanceof FormView && $request->$property->has($var->vars['name'])) {
                        $this->collectFormErrors($var);
                        break;
                    }
                }
            }
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onAjaxResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if (count($this->formErrors)) {
            $response->headers->set('X-SF-FormErrors', json_encode($this->formErrors));
        }

        if ($this->elementBag->count()) {
            $response->headers->set('X-SF-Elements', json_encode($this->elementBag->peekAll()));
        }
    }

    /**
     * @param ExtensionInterface $component
     */
    public function addComponent(ExtensionInterface $component)
    {
        $this->components[] = $component;
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
     * @param ExtensionInterface $plugin
     */
    public function addPlugin(ExtensionInterface $plugin)
    {
        $this->plugins[] = $plugin;
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

    /**
     * @param FormView $form
     */
    protected function collectFormErrors(FormView $form)
    {
        $formErrors = array(
            'form' => array(),
            'children' => array(),
        );
        $this->processChildrenRecursive($formErrors, $form);
        $this->formErrors = $formErrors;
    }

    /**
     * @param $formErrors
     * @param FormView $element
     */
    protected function processChildrenRecursive(&$formErrors, FormView $element)
    {
        if (count($element->vars['errors'])) {
            $value = array(
                'error_type' => $element->vars['error_type'],
                'errors' => array_map(function($error) {
                    /** @var $error FormError */
                    return $error->getMessage();
                }, $element->vars['errors']),
            );

            if (!isset($element->parent)) {
                $formErrors['form'] = $value;
            } else {
                $formErrors['children'][$element->vars['full_name']] = $value;
            }
        }

        foreach ($element->children as $child) {
            $this->processChildrenRecursive($formErrors, $child);
        }
    }
}