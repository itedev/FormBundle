<?php

namespace ITE\FormBundle\Form\Type\Plugin\FileUploader;

use ITE\FormBundle\File\UploadedFile;
use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\FileUploaderPlugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AjaxFileType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxFileType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * @var string
     */
    private $uploadPath;

    /**
     * @param string $uploadPath
     * @param array $options
     */
    public function __construct($uploadPath, array $options = [])
    {
        parent::__construct($options);
        $this->uploadPath = $uploadPath;
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->setOption('plugins', [
            FileUploaderPlugin::getName() => [
                'extras' => (object) [],
                'options' => (object) array_replace_recursive($this->options, $options['plugin_options'], [
                    'limit' => $options['multiple'] ? null : 1,
                    'inputNameBrackets' => false,
                    'upload' => [
                        'url' => $options['url'],
                        'start' => true,
                    ],
                    'listInput' => false,
                ])
            ],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'route' => 'ite_form_plugin_fileuploader_file_upload',
        ]);
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
        return 'ite_fileuploader_ajax_file';
    }
}
