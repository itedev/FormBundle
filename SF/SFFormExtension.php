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
class SFFormExtension extends SFExtension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

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
     * @param array $inputs
     * @return array
     */
    public function modifyJavascripts(array &$inputs)
    {
        $newInputs = array();

        // add component js
        $bundlePath = $this->container->get('kernel')->getBundle('ITEFormBundle')->getPath();
        foreach (SFForm::$components as $component) {
            $enabled = $this->container->getParameter(sprintf('ite_form.component.%s.enabled', $component));
            if ($enabled && file_exists(sprintf('%s/Resources/public/js/component/%s.js',
                      $bundlePath,  $component))) {
                $newInputs[] = sprintf('@ITEFormBundle/Resources/public/js/component/%s.js', $component);
            }
        }

        // add plugin js
        foreach (SFForm::$plugins as $plugin) {
            $enabled = $this->container->getParameter(sprintf('ite_form.plugin.%s.enabled', $plugin));
            if ($enabled) {
                $newInputs[] = sprintf('@ITEFormBundle/Resources/public/js/plugin/%s.js', $plugin);
            }
        }

        if (false !== $index = array_search('@ITEFormBundle/Resources/public/js/sf.form.js', $inputs)) {
            array_splice(
                $inputs,
                $index + 1,
                0,
                $newInputs
            );
        } else {
            $inputs = array_merge($inputs, $newInputs);
        }
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
    public function onKernelView(GetResponseForControllerResultEvent $event)
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
    public function onKernelResponse(FilterResponseEvent $event)
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