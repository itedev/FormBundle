<?php

namespace ITE\FormBundle\Form\Type\Hidden;

use ITE\FormBundle\Form\Type\Core\AbstractTimeType;

/**
 * Class TimeHiddenType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TimeHiddenType extends AbstractTimeType
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
        return 'ite_time_hidden';
    }
}