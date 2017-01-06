<?php

namespace ITE\FormBundle\Form\Type\Hidden;

use Symfony\Component\Form\Extension\Core\Type\PercentType;

/**
 * Class PercentHiddenType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class PercentHiddenType extends PercentType
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
        return 'ite_percent_hidden';
    }
}
