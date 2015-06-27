<?php

namespace ITE\FormBundle\Form\Type\Core;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractDateTimeType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractDateTimeType extends DateTimeType
{
    const DEFAULT_FORMAT = "yyyy-MM-dd HH:mm:ss";

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults([
            'format' => self::DEFAULT_FORMAT,
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