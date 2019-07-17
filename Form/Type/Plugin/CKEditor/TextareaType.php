<?php

namespace ITE\FormBundle\Form\Type\Plugin\CKEditor;

use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\CKEditorPlugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class TextareaType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TextareaType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->setOption(CKEditorPlugin::getName(), [
            'extras' => (object) [],
            'options' => array_replace_recursive($this->options, $options['plugin_options']),
        ]);
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
        return 'ite_ckeditor_textarea';
    }
}
