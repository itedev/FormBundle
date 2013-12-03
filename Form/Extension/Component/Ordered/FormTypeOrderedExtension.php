<?php

namespace ITE\FormBundle\Form\Extension\Component\Ordered;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeOrderedExtension
 * @package ITE\FormBundle\Form\Extension\Component\Ordered
 */
class FormTypeOrderedExtension extends AbstractTypeExtension
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
        return 'form';
    }
}