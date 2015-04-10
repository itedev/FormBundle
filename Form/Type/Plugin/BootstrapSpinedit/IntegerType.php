<?php

namespace ITE\FormBundle\Form\Type\Plugin\BootstrapSpinedit;

use ITE\FormBundle\SF\Plugin\BootstrapSpineditPlugin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class IntegerType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class IntegerType extends AbstractType
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

        $view->vars['plugins'][BootstrapSpineditPlugin::getName()] = array(
            'extras' => (object) array(),
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'], array(
                    'numberOfDecimals' => 0,
                ))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'integer';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_bootstrap_spinedit_integer';
    }
}