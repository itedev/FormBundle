<?php

namespace ITE\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractDynamicType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AbstractDynamicType extends AbstractType
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $parent;

    /**
     * @var array
     */
    private $options;

    /**
     * @param string $type
     * @param string $parent
     * @param array $options
     */
    public function __construct($type, $parent, array $options = [])
    {
        $this->type = $type;
        $this->parent = $parent;
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
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->type;
    }
}
