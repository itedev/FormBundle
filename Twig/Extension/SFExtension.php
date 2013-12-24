<?php

namespace ITE\FormBundle\Twig\Extension;

use ITE\FormBundle\SF\SFFormExtensionInterface;
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
            new \Twig_SimpleFunction('ite_uniqid', array($this, 'uniqId')),
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
     * @return mixed
     */
    public function parentFormResource($filename = null)
    {
        if (isset($filename)) {
            $index = array_search($filename, $this->formResources);

            return $this->formResources[--$index];
        }

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
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.extension.sf';
    }

}