<?php

namespace ITE\FormBundle\Twig\Extension;

use ITE\JsBundle\Service\SFInterface;
use Symfony\Component\Form\FormView;
use Twig_Environment;
use Twig_Extension;
use Symfony\Component\Locale\Locale;

/**
 * Class SFFormExtension
 * @package ITE\FormBundle\Twig\Extension
 */
class SFFormExtension extends Twig_Extension
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
            new \Twig_SimpleFunction('ite_form_collection_id', array($this, 'getCollectionId')),
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
    public function getCollectionId(FormView $view)
    {
        $id = $view->vars['id'];
        while (null !== $view->parent) {
            $view = $view->parent;
            if (in_array('collection', $view->vars['block_prefixes'])) {

            }
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.sf_form_extension';
    }

}