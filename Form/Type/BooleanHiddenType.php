<?php

namespace ITE\FormBundle\Form\Type;

use ITE\FormBundle\Form\DataTransformer\BooleanToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class BooleanHiddenType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class BooleanHiddenType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new BooleanToStringTransformer($options['required']));
    }

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
        return 'ite_boolean_hidden';
    }
}