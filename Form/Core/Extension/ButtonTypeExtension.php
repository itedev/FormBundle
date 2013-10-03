<?php

namespace ITE\FormBundle\Form\Core\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ButtonTypeExtension
 * @package ITE\FormBundle\Form\Core\Extension
 */
class ButtonTypeExtension extends AbstractTypeExtension
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'position' => null,
        ));
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return 'button';
    }
}