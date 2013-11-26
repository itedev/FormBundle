<?php

namespace ITE\FormBundle\Twig\Extension;

use Twig_Environment;
use Twig_Extension;
use Twig_Template;

/**
 * Class EditableExtension
 * @package ITE\FormBundle\Twig\Extension
 */
class EditableExtension extends Twig_Extension
{
    /**
     *
     */
    public function __construct(EntityManager $em)
    {

    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ite_editable', array($this, 'editable'), array()),
        );
    }

    public function editable($entity, $field)
    {

    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.editable_extension';
    }

}