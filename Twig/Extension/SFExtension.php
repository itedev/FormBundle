<?php

namespace ITE\FormBundle\Twig\Extension;

use ITE\JsBundle\SF\SFExtensionInterface;
use ReflectionObject;
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
     * @var SFExtensionInterface
     */
    protected $sfForm;

    /**
     * @var array $formResources
     */
    protected $formResources;

    /**
     * @param SFExtensionInterface $sfForm
     * @param $formResources
     */
    public function __construct(SFExtensionInterface $sfForm, $formResources)
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
            new \Twig_SimpleFunction('ite_uniqid', array($this, 'uniqId')),
            new \Twig_SimpleFunction('ite_parent_form_resource', array($this, 'parentFormResource'))
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
     * @param string $prefix
     * @return string
     */
    public function uniqId($prefix = '')
    {
        return uniqid($prefix);
    }

    /**
     * @param $filename
     * @return mixed
     */
    public function parentFormResource($filename)
    {
        $index = array_search($filename, $this->formResources);

        return $this->formResources[--$index];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.sf_extension';
    }

}