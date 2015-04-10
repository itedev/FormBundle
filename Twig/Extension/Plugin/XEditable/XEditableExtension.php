<?php

namespace ITE\FormBundle\Twig\Extension\Plugin\XEditable;

use ITE\FormBundle\Service\Editable\Plugin\XEditable\FieldMapper;
use Twig_Environment;
use Twig_Extension;
use Twig_Template;

/**
 * Class XEditableExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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
        $text = isset($text) ? (string) $text : null;

        $parameters = $this->fieldMapper->resolveParameters($entity, $field, $text, $options);

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
        return 'ite_form.twig.extension.plugin.x_editable';
    }

}