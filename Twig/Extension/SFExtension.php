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
            new \Twig_SimpleFunction('ite_form_sf_add_element', array($this, 'sfAddElement')),
        );
    }

    /**
     * @param $plugin
     * @param $selector
     * @param $options
     */
    public function sfAddElement($plugin, $selector, $options)
    {
        $this->sfForm->addElement($plugin, $selector, $options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.sf_extension';
    }

}