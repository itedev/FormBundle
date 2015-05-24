<?php

namespace ITE\FormBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class SimpleRangeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class SimpleRangeType extends AbstractRangeType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        // need to call parent method!
        parent::setDefaultOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_simple_range';
    }
}