<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TimeTypeTwelveHourExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TimeTypeTwelveHourExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'twelve_hour' => false,
        ]);
        $resolver->setAllowedTypes([
            'twelve_hour' => ['bool'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'time';
    }
}
