<?php

namespace ITE\FormBundle\Form\Type\Core;

use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractTimeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractTimeType extends TimeType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults([
            'widget' => 'single_text',
            'html5' => false,
        ]);

        $resolver->setAllowedValues([
            'input' => [
                'datetime',
                'string',
                'timestamp',
            ],
            'widget' => ['single_text'],
            'html5' => [false],
        ]);
    }
}
