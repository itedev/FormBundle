<?php

namespace ITE\FormBundle\Form\Type\Plugin\Core;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractIntegerPluginType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractIntegerPluginType extends IntegerType
{
    /**
     * @var array $options
     */
    protected $options;

    /**
     * @param $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults([
            'plugin_options' => [],
        ]);
        $resolver->setAllowedTypes([
            'plugin_options' => ['array'],
        ]);
    }
}