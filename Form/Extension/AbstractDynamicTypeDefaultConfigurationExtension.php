<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractDynamicTypeDefaultConfigurationExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AbstractDynamicTypeDefaultConfigurationExtension extends AbstractTypeExtension
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $type
     * @param array $options
     */
    public function __construct($type, array $options = [])
    {
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults($this->options);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return $this->type;
    }
}
