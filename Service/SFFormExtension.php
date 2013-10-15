<?php

namespace ITE\FormBundle\Service;

use ITE\JsBundle\Service\SFExtensionInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class SFFormExtension implements SFExtensionInterface
{
    /**
     * Plugins
     */
    const PLUGIN_SELECT2 = 'select2';
    const PLUGIN_TINYMCE = 'tinymce';

    protected static $plugins = array(
        self::PLUGIN_SELECT2,
        self::PLUGIN_TINYMCE,
    );

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
     * @return string
     */
    public function dump()
    {
        $dump = '';
        if ($this->elementBag->count()) {
            $dump .= 'SF.elements.set(' . json_encode($this->elementBag->peekAll()) . ');';
        }
        $dump .= 'SF.elements.apply();';
        return $dump;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $result = $event->getControllerResult();

        // is form was submitted via ajax - get its errors if exist
        $property = 'POST' === $request->getMethod() ? 'request' : 'query';
        foreach ($result as $var) {
            if ($var instanceof FormView && $request->$property->has($var->vars['name'])) {
                $this->collectFormErrors($var);
                break;
            }
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if (count($this->formErrors)) {
            $response->headers->set('X-SF-FormErrors', json_encode($this->formErrors));
        }
    }

    /**
     * @param $plugin
     * @param $id
     * @param $options
     */
    public function addElement($plugin, $id, $options)
    {
        $this->elementBag->addElement($plugin, $id, $options);
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

    /**
     * @return array
     */
    public static function getPlugins()
    {
        return self::$plugins;
    }
}