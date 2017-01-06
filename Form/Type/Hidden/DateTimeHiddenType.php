<?php

namespace ITE\FormBundle\Form\Type\Hidden;

use ITE\FormBundle\Form\Type\Core\AbstractDateTimeType;

/**
 * Class DateTimeHiddenType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DateTimeHiddenType extends AbstractDateTimeType
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
        return 'ite_datetime_hidden';
    }
}
