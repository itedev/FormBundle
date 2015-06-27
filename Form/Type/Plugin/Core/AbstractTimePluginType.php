<?php

namespace ITE\FormBundle\Form\Type\Plugin\Core;

use ITE\FormBundle\Form\Type\Core\AbstractTimeType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractTimePluginType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractTimePluginType extends AbstractTimeType
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