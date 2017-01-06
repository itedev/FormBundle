<?php

namespace ITE\FormBundle\Form\Type\Plugin\Fileupload;

use ITE\FormBundle\SF\Plugin\FileuploadPlugin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FileType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FileType extends AbstractType
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
        $resolver->setDefaults([
            'route' => 'ite_form_plugin_fileupload_file_upload',
            'input_name' => 'files',
            'widget' => 'basic',
        ]);
        $resolver->setAllowedValues([
            'widget' => [
                'basic',
                'basic_plus',
                'basic_plus_ui',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $pluginOptions = array_replace_recursive(
            [
                'uploadTemplateId' => null,
                'downloadTemplateId' => null,
            ],
            $this->options,
            $options['plugin_options'],
            [
                'url' => $view->vars['url'],
                'paramName' => $options['input_name'],
            ]
        );

        if (!$options['multiple']) {
            $pluginOptions['maxNumberOfFiles'] = 1;
        }

        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = [];
        }
        $view->vars['plugins'][FileuploadPlugin::getName()] = [
            'extras' => (object) [],
            'options' => $pluginOptions,
        ];
        $view->vars['widget'] = $options['widget'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_ajax_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_fileupload_file';
    }
}
