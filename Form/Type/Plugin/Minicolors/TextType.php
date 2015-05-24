<?php

namespace ITE\FormBundle\Form\Type\Plugin\Minicolors;

use ITE\FormBundle\Form\Type\Plugin\AbstractPluginType;
use ITE\FormBundle\SF\Plugin\MinicolorsPlugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class TextType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TextType extends AbstractPluginType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['plugins'][MinicolorsPlugin::getName()] = [
            'extras' => (object) [],
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_minicolors_text';
    }
}