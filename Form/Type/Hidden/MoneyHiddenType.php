<?php

namespace ITE\FormBundle\Form\Type\Hidden;

use ITE\FormBundle\Form\DataTransformer\BooleanToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MoneyHiddenType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MoneyHiddenType extends MoneyType
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
        return 'ite_money_hidden';
    }
}