<?php

namespace ITE\FormBundle\Form\Type\Plugin\Fileupload;

use ITE\FormBundle\Form\EventListener\Plugin\Fileupload\FileuploadSubscriber;
use ITE\FormBundle\Service\File\FileManagerInterface;
use ITE\FormBundle\Service\File\WebFile;
use ITE\FormBundle\Util\UrlUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

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
     * @var RouterInterface $router
     */
    protected $router;

    /**
     * @var FileManagerInterface
     */
    protected $fileManager;

    /**
     * @var StorageInterface $vichUploaderStorage
     */
    protected $vichUploaderStorage;

    /**
     * @param $options
     * @param RouterInterface $router
     * @param FileManagerInterface $fileManager
     * @param StorageInterface $vichUploaderStorage
     */
    public function __construct($options, RouterInterface $router, FileManagerInterface $fileManager, StorageInterface $vichUploaderStorage = null)
    {
        $this->options = $options;
        $this->router = $router;
        $this->fileManager = $fileManager;
        $this->vichUploaderStorage = $vichUploaderStorage;
    }

    /**
     * Get router
     *
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $type = $this;
        $url = function(Options $options) use ($type) {
            return $type->getRouter()->generate($options['route'], $options['route_parameters']);
        };

        $resolver->setDefaults(array(
            'route' => 'ite_form_plugin_fileupload_file_upload',
            'route_parameters' => array(),
            'url' => $url,
            'widget' => 'basic',
            'extras' => array(),
            'plugin_options' => array(),
        ));
        $resolver->setAllowedTypes(array(
            'extras' => array('array'),
            'plugin_options' => array('array'),
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new FileuploadSubscriber($this->fileManager));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $root = $this->getRootView($view);
        if (!isset($root->vars['ajax_token']) || empty($root->vars['ajax_token'])) {
            throw new \RuntimeException(sprintf(
                'Unable to retrieve ajax token value. Maybe you forgot to add "%s" option in your root form?',
                'ajax_token'
            ));
        }

        $ajaxToken = $root->vars['ajax_token_value'];

        $pluginOptions = array_replace_recursive(array(
            'paramName' => 'files',
            'uploadTemplateId' => null,
            'downloadTemplateId' => null,
        ), $this->options, $options['plugin_options']);

        $url = $this->router->generate($options['route'], array_replace(
            $options['route_parameters'],
            array(
                'ajaxToken' => $ajaxToken,
                'paramName' => $pluginOptions['paramName'],
                'multiple' => $options['multiple'] ? 1 : 0,
            )
        ));

        if (!$options['multiple']) {
            $pluginOptions['maxNumberOfFiles'] = 1;
        }

        $view->vars['element_data'] = array(
            'extras' => (object) $options['extras'],
            'options' => array_replace_recursive($pluginOptions, array(
                'url' => $url,
            ))
        );

        $view->vars['widget'] = $options['widget'];

        $files = $this->getFile($form);
        $ajaxFiles = $this->fileManager->getFiles($ajaxToken, $view->vars['full_name']); // @todo: add multiple check
        $view->vars['uploaded_files'] = array_merge($files, $ajaxFiles);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_fileupload_file';
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    protected function getFile(FormInterface $form)
    {
        // @todo: add check if vichuploader support this entity
        if (isset($this->vichUploaderStorage)
            && $form->getData() instanceof File
            && $form->getConfig()->getMapped()
            && $form->getParent()
            && $object = $form->getParent()->getData()) {
            $field = $form->getConfig()->getName();

            $path = $this->vichUploaderStorage->resolvePath($object, $field);
            $uri = $this->vichUploaderStorage->resolveUri($object, $field);

            return array(
                new WebFile($path, $uri)
            );
        }
        return array();
    }

    /**
     * @param FormView $view
     * @return FormView
     */
    protected function getRootView(FormView $view)
    {
        $root = $view;
        while (null !== $root->parent) {
            $root = $root->parent;
        }

        return $root;
    }
}