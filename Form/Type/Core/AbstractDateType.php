<?php

namespace ITE\FormBundle\Form\Type\Core;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractDateType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractDateType extends DateType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults([
            'widget' => 'single_text',
            'format' => self::HTML5_FORMAT,
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