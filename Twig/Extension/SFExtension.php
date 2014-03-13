<?php

namespace ITE\FormBundle\Twig\Extension;

use ITE\FormBundle\SF\SFFormExtensionInterface;
use Symfony\Component\Form\FormView;
use Twig_Environment;
use Twig_Extension;
use Twig_Template;

/**
 * Class SFExtension
 * @package ITE\FormBundle\Twig\Extension
 */
class SFExtension extends Twig_Extension
{
    /**
     * @var SFFormExtensionInterface
     */
    protected $sfForm;

    /**
     * @var array $formResources
     */
    protected $formResources;

    /**
     * @param SFFormExtensionInterface $sfForm
     * @param $formResources
     */
    public function __construct(SFFormExtensionInterface $sfForm, $formResources)
    {
        $this->sfForm = $sfForm;
        $this->formResources = $formResources;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('ite_form_sf_add_plugin_element', array($this, 'sfAddPluginElement')),
            new \Twig_SimpleFunction('ite_parent_form_resource', array($this, 'parentFormResource')),
            new \Twig_SimpleFunction('ite_last_form_resource', array($this, 'lastFormResource')),
            new \Twig_SimpleFunction('ite_uniqid', array($this, 'uniqId')),
            new \Twig_SimpleFunction('ite_dynamic_form_widget', array($this, 'dynamicFormWidget'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('ite_dynamic_form_row', array($this, 'dynamicFormRow'), array('is_safe' => array('html'), 'needs_environment' => true)),
        );
    }

    /**
     * @param $selector
     * @param $plugin
     * @param $pluginData
     */
    public function sfAddPluginElement($selector, $plugin, $pluginData)
    {
        $this->sfForm->getElementBag()->addPluginElement($selector, $plugin, $pluginData);
    }

    /**
     * @param $filename
     * @return string
     */
    public function parentFormResource($filename)
    {
        $index = array_search($filename, $this->formResources);

        return $this->formResources[--$index];
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
     * @param Twig_Environment $env
     * @param FormView $view
     * @param $newType
     * @param array $variables
     * @return mixed
     */
    public function dynamicFormWidget(Twig_Environment $env, FormView $view, $newType, $variables = array())
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
    public function dynamicFormRow(Twig_Environment $env, FormView $view, $newType, $variables = array())
    {
        return $this->dynamicFormElement($env, 'row', $view, $newType, $variables);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.extension.sf';
    }

    /**
     * @param Twig_Environment $env
     * @param $blockNameSuffix
     * @param FormView $view
     * @param $newType
     * @param array $variables
     * @return mixed
     */
    private function dynamicFormElement(Twig_Environment $env, $blockNameSuffix, FormView $view, $newType, $variables = array())
    {
        array_splice($view->vars['block_prefixes'], 1, count($view->vars['block_prefixes']), array($newType));

        return $env->getExtension('form')->renderer->searchAndRenderBlock($view, $blockNameSuffix, $variables);
    }

}