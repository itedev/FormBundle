<?php

namespace ITE\FormBundle\Twig\Extension;

use ITE\JsBundle\SF\SFExtensionInterface;
use Twig_Environment;
use Twig_Extension;

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
     * @param SFExtensionInterface $sfForm
     */
    public function __construct(SFExtensionInterface $sfForm)
    {
        $this->sfForm = $sfForm;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('ite_form_sf_add_plugin_element', array($this, 'sfAddPluginElement')),
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
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.sf_extension';
    }

}