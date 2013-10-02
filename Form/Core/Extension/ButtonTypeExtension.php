<?php

namespace ITE\FormBundle\Form\Core\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ButtonTypeExtension extends AbstractTypeExtension
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'weight' => 0,
        ));
    }

    public function getExtendedType()
    {
        return 'button';
    }
}