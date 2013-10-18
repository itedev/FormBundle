<?php

namespace ITE\FormBundle\Form\Core\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TinymceTextareaType
 * @package ITE\FormBundle\Form\Core\Type
 */
class TinymceTextareaType extends AbstractType
{
    /**
     * @var array $extras
     */
    protected $extras;

    /**
     * @var array $options
     */
    protected $options;

    /**
     * @param $extras
     * @param $options
     */
    public function __construct($extras, $options)
    {
        $this->extras = $extras;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'extras' => array(),
            'plugin_options' => array(),
        ));
        $resolver->setAllowedTypes(array(
            'extras' => array('array'),
            'plugin_options' => array('array'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['element_data'] = array(
            'extras' => (object) array_replace_recursive($this->extras, $options['extras']),
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'])
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_tinymce_textarea';
    }
}