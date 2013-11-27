<?php

namespace ITE\FormBundle\Twig\Extension;

use ITE\FormBundle\Service\Editable\Plugin\XEditable\FieldMapper;
use Twig_Environment;
use Twig_Extension;
use Twig_Template;

/**
 * Class XEditableExtension
 * @package ITE\FormBundle\Twig\Extension
 */
class XEditableExtension extends Twig_Extension
{
    /**
     * @var FieldMapper $fieldMapper
     */
    protected $fieldMapper;

    /**
     * @param FieldMapper $fieldMapper
     */
    public function __construct(FieldMapper $fieldMapper)
    {
        $this->fieldMapper = $fieldMapper;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ite_x_editable', array($this, 'xEditable'), array('is_safe' => array('html'), 'needs_environment' => true)),
        );
    }

    /**
     * @param Twig_Environment $env
     * @param $entity
     * @param $field
     * @param $options
     * @return string
     */
    public function xEditable(Twig_Environment $env, $entity, $field, $options = array())
    {
        $parameters = $this->fieldMapper->resolveParameters($entity, $field, $options);

        return $env->render('ITEFormBundle:Form/Plugin/x_editable:field.html.twig', $parameters);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.x_editable_extension';
    }

}