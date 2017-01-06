<?php

namespace ITE\FormBundle\Form\Type\Hidden;

use ITE\FormBundle\Form\Type\Core\AbstractDateType;

/**
 * Class DateHiddenType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DateHiddenType extends AbstractDateType
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
        return 'ite_date_hidden';
    }
}
