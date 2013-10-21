<?php

namespace ITE\FormBundle\Twig\Extension;

use ITE\JsBundle\Service\SFInterface;
use Symfony\Component\Form\FormView;
use Twig_Environment;
use Twig_Extension;
use Symfony\Component\Locale\Locale;

/**
 * Class SFExtension
 * @package ITE\FormBundle\Twig\Extension
 */
class SFExtension extends Twig_Extension
{
    /**
     * @var SFInterface
     */
    protected $sf;

    /**
     * @param SFInterface $sf
     */
    public function __construct(SFInterface $sf)
    {
        $this->sf = $sf;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('ite_form_sf_add_element', array($this, 'sfAddElement')),
            new \Twig_SimpleFunction('ite_form_test', array($this, 'test')),
        );
    }

    /**
     * @param $plugin
     * @param $selector
     * @param $options
     */
    public function sfAddElement($plugin, $selector, $options)
    {
        $this->sf->getExtension('form')->addElement($plugin, $selector, $options);
    }

    /**
     * @param FormView $view
     */
    public function test(FormView $view)
    {
        $a = 1;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.sf_extension';
    }

}