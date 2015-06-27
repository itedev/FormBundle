<?php

namespace ITE\FormBundle\Form\Type\Hidden;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * Class IntegerHiddenType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class IntegerHiddenType extends IntegerType
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
        return 'ite_integer_hidden';
    }
}