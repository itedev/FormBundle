<?php

namespace ITE\FormBundle\Form\Type\Hidden;

use Symfony\Component\Form\Extension\Core\Type\NumberType;

/**
 * Class NumberHiddenType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class NumberHiddenType extends NumberType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_number_hidden';
    }
}