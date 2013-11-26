<?php

namespace ITE\FormBundle\Form\Type\Plugin\StarRating;

use Symfony\Component\Form\AbstractType as BaseAbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class RatingType
 * @package ITE\FormBundle\Form\Type\Plugin\Select2
 */
class RatingType extends BaseAbstractType
{
    /**
     * @var array $options
     */
    protected $options;

    /**
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'plugin_options' => array(),
            'expanded' => true,
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
        ));
        $resolver->setAllowedValues(array(
            'expanded' => array(true),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['element_data'] = array(
            'extras' => (object) array(),
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_starrating_rating';
    }
}