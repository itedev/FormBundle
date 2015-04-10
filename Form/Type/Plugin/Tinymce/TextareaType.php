<?php

namespace ITE\FormBundle\Form\Type\Plugin\Tinymce;

use ITE\FormBundle\SF\Plugin\TinymcePlugin;
use ITE\Common\Util\ArrayUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class TextareaType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TextareaType extends AbstractType
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
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = array();
        }
        $view->vars['plugins'][TinymcePlugin::getName()] = array(
            'extras' => (object) array(),
            'options' => (object) ArrayUtils::replaceRecursive($this->options, $options['plugin_options']),
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