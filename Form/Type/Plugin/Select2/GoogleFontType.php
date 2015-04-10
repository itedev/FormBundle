<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\Form\ChoiceList\GoogleFontChoiceBuilder;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\AbstractType as BaseAbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class GoogleFontType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class GoogleFontType extends BaseAbstractType
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
        $view->vars['plugins'][Select2Plugin::getName()] = array(
            'extras' => array(
                'google_fonts' => true
            ),
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'])
        );

        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            0,
            'ite_select2'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_google_font';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_select2_google_font';
    }
}