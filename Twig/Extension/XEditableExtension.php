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
     * @var array $options
     */
    protected $options;

    /**
     * @var FieldMapper $fieldMapper
     */
    protected $fieldMapper;

    /**
     * @param $options
     * @param FieldMapper $fieldMapper
     */
    public function __construct($options, FieldMapper $fieldMapper)
    {
        $this->options = $options;
        $this->fieldMapper = $fieldMapper;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ite_x_editable', array($this, 'xEditable'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
        );
    }

    /**
     * @param Twig_Environment $env
     * @param $entity
     * @param $field
     * @param null $text
     * @param array $options
     * @param array $attr
     * @return string
     */
    public function xEditable(Twig_Environment $env, $entity, $field, $text = null, $options = array(), $attr = array())
    {
        if (isset($text) && is_object($text)) {
            $text = (string) $text;
        }

        $parameters = $this->fieldMapper->resolveParameters($entity, $field, $text, array_replace_recursive(
            $this->options, $options
        ));

        if (!isset($attr['id'])) {
            $attr['id'] = uniqid('x_editable_');
        }
        $parameters['attr'] = $attr;

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