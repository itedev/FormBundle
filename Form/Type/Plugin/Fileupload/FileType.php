<?php

namespace ITE\FormBundle\Form\Type\Plugin\Fileupload;

use ITE\FormBundle\SF\Plugin\FileuploadPlugin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FileType
 * @package ITE\FormBundle\Form\Type\Plugin\Fileupload
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
        $resolver->setDefaults(array(
            'route' => 'ite_form_plugin_fileupload_file_upload',
            'input_name' => 'files',
            'widget' => 'basic',
        ));
        $resolver->setAllowedValues(array(
            'widget' => array(
                'basic',
                'basic_plus',
                'basic_plus_ui',
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $pluginOptions = array_replace_recursive(
            array(
                'uploadTemplateId' => null,
                'downloadTemplateId' => null,
            ),
            $this->options,
            $options['plugin_options'],
            array(
                'url' => $view->vars['url'],
                'paramName' => $options['input_name'],
            )
        );

        if (!$options['multiple']) {
            $pluginOptions['maxNumberOfFiles'] = 1;
        }

        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = array();
        }
        $view->vars['plugins'][FileuploadPlugin::getName()] = array(
            'extras' => (object) array(),
            'options' => $pluginOptions,
        );
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