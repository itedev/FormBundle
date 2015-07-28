<?php

namespace ITE\FormBundle\Twig\Extension;

use ITE\Common\Util\ReflectionUtils;
use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Twig_Environment;
use Twig_Extension;
use Twig_Template;

/**
 * Class SFExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class SFExtension extends Twig_Extension
{
    /**
     * @var array $formResources
     */
    protected $formResources;

    /**
     * @var PropertyAccessor
     */
    protected $accessor;

    /**
     * @param $formResources
     */
    public function __construct($formResources)
    {
        $this->formResources = $formResources;
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('ite_parent_form_resource', [$this, 'parentFormResource']),
            new \Twig_SimpleFunction('ite_last_form_resource', [$this, 'lastFormResource']),
            new \Twig_SimpleFunction('ite_uniqid', [$this, 'uniqId']),
            new \Twig_SimpleFunction('ite_set_attribute', [$this, 'setAttribute']),
            new \Twig_SimpleFunction('ite_set_not_rendered', [$this, 'setNotRendered']),
            new \Twig_SimpleFunction('ite_dynamic_form_widget', [$this, 'dynamicFormWidget'], ['is_safe' => ['html'], 'needs_environment' => true]),
            new \Twig_SimpleFunction('ite_dynamic_form_row', [$this, 'dynamicFormRow'], ['is_safe' => ['html'], 'needs_environment' => true]),
        ];
    }

    /**
     * @param $filename
     * @return string
     */
    public function parentFormResource($filename = null)
    {
        if (isset($filename)) {
            $filename = (string) $filename;
            $index = array_search($filename, $this->formResources);

            return $this->formResources[--$index];
        }

        return end($this->formResources);
    }

    /**
     * @return string
     */
    public function lastFormResource()
    {
        return end($this->formResources);
    }

    /**
     * @param string $prefix
     * @return string
     */
    public function uniqId($prefix = '')
    {
        return uniqid($prefix);
    }

    /**
     * @param $object
     * @param $attributeName
     * @param $attributeValue
     */
    public function setAttribute($object, $attributeName, $attributeValue)
    {
        $this->accessor->setValue($object, $attributeName, $attributeValue);
    }

    /**
     * @param FormView $view
     */
    public function setNotRendered(FormView $view)
    {
        ReflectionUtils::setValue($view, 'rendered', false);
    }

    /**
     * @param Twig_Environment $env
     * @param FormView $view
     * @param $newType
     * @param array $variables
     * @return mixed
     */
    public function dynamicFormWidget(Twig_Environment $env, FormView $view, $newType, $variables = [])
    {
        return $this->dynamicFormElement($env, 'widget', $view, $newType, $variables);
    }

    /**
     * @param Twig_Environment $env
     * @param FormView $view
     * @param $newType
     * @param array $variables
     * @return mixed
     */
    public function dynamicFormRow(Twig_Environment $env, FormView $view, $newType, $variables = [])
    {
        return $this->dynamicFormElement($env, 'row', $view, $newType, $variables);
    }

    /**
     * @param Twig_Environment $env
     * @param $blockNameSuffix
     * @param FormView $view
     * @param $newType
     * @param array $variables
     * @return mixed
     */
    private function dynamicFormElement(Twig_Environment $env, $blockNameSuffix, FormView $view, $newType, $variables = [])
    {
        array_splice($view->vars['block_prefixes'], 1, count($view->vars['block_prefixes']), [$newType]);

        return $env->getExtension('form')->renderer->searchAndRenderBlock($view, $blockNameSuffix, $variables);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.extension.sf';
    }

}