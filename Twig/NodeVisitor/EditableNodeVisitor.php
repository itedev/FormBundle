<?php

namespace ITE\FormBundle\Twig\NodeVisitor;

use Twig_Environment;
use Twig_NodeInterface;

/**
 * Class EditableNodeVisitor
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class EditableNodeVisitor implements \Twig_NodeVisitorInterface
{
    /**
     * {@inheritdoc}
     */
    public function enterNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        if ($node instanceof \Twig_Node_Expression_Filter
            && 'ite_editable' === $node->getNode('filter')->getAttribute('value')) {
            $propertyPath = null;
            if ($node->getNode('node') instanceof \Twig_Node_Expression_GetAttr) {
                $getAttrNode = $node->getNode('node');
                $object = $env->compile($getAttrNode->getNode('node'));
                $property = $getAttrNode->getNode('attribute')->getAttribute('value');

                $propertyPath = [
                    'object' => $object,
                    'property' => $property,
                ];
            }

            $node->setAttribute('property_path', $propertyPath);
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function leaveNode(Twig_NodeInterface $node, Twig_Environment $env)
    {
        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }
}
