<?php

namespace ITE\FormBundle\Form\Type\Plugin\Tinymce;

use ITE\FormBundle\Form\Type\Plugin\AbstractPluginType;
use ITE\FormBundle\SF\Plugin\TinymcePlugin;
use ITE\Common\Util\ArrayUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class TextareaType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TextareaType extends AbstractPluginType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['plugins'][TinymcePlugin::getName()] = [
            'extras' => (object) [],
            'options' => (object) ArrayUtils::replaceRecursive($this->options, $options['plugin_options']),
        ];
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