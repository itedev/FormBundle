<?php

namespace ITE\FormBundle\Form\Extension\Component\Ordered;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ButtonTypeOrderedExtension
 * @package ITE\FormBundle\Form\Extension\Component\Ordered
 */
class ButtonTypeOrderedExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'position' => null,
        ));
        $resolver->setAllowedTypes(array(
            'position' => array('null', 'string', 'array')
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'button';
    }
}