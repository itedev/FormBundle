<?php

namespace ITE\FormBundle\Twig\Extension\Component\Collection;

use Symfony\Component\Form\FormView;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Twig_Environment;
use Twig_Extension;
use Twig_Template;

/**
 * Class CollectionExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CollectionExtension extends Twig_Extension
{
    /**
     * @var PropertyAccessor $propertyAccessor
     */
    private $propertyAccessor;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('ite_is_collection', array($this, 'isCollection'), array('needs_context' => true)),
            new \Twig_SimpleFunction('ite_is_collection_item', array($this, 'isCollectionItem'), array('needs_context' => true)),
        );
    }

    /**
     * @param $context
     * @return bool
     */
    public function isCollection($context)
    {
        $blockPrefixes = $this->propertyAccessor->getValue($context, '[vars][block_prefixes]');

        return in_array('collection', $blockPrefixes);
    }

    /**
     * @param $context
     * @param FormView $view
     * @return bool
     */
    public function isCollectionItem($context, FormView $view = null)
    {
        $blockPrefixes = isset($view)
            ? $this->propertyAccessor->getValue($view, 'parent.vars[block_prefixes]')
            : $this->propertyAccessor->getValue($context, '[form].parent.vars[block_prefixes]');

        return in_array('collection', $blockPrefixes);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.collection_extension';
    }

}