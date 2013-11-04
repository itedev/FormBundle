<?php

namespace ITE\FormBundle\Form\Type\Plugin\Fileupload;

use ITE\FormBundle\Form\EventListener\Plugin\Fileupload\FileuploadSubscriber;
use ITE\FormBundle\Service\File\FileManagerInterface;
use ITE\FormBundle\Util\UrlUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

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
     * @param $options
     * @param RouterInterface $router
     * @param FileManagerInterface $fileManager
     */
    public function __construct($options, RouterInterface $router, FileManagerInterface $fileManager)
    {
        $this->options = $options;
        $this->router = $router;
        $this->fileManager = $fileManager;
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
        $url = function (Options $options) use ($type) {
            return $type->getRouter()->generate($options['route'], $options['route_parameters']);
        };

        $resolver->setDefaults(array(
                'route' => 'ite_form_plugin_fileupload_file_upload',
                'route_parameters' => array(),
                'url' => $url,
                'extras' => array(),
                'plugin_options' => array(),
            ));
        $resolver->setAllowedTypes(array(
                'extras' => array('array'),
                'plugin_options' => array('array'),
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
        $url = $options['url'];
        if (isset($root->vars['ajax_token']) && !empty($root->vars['ajax_token'])) {
            $url = UrlUtils::addGetParameter(
                $url,
                $root->vars['ajax_token_field_name'],
                $root->vars['ajax_token_value']
            );
        }

        $view->vars['element_data'] = array(
            'extras' => (object) $options['extras'],
            'options' => array_replace_recursive(array(
                    'paramName' => 'files',
                    'uploadTemplateId' => null,
                    'downloadTemplateId' => null,
                ), $this->options, $options['plugin_options'], array(
                    'url' => $url
                ))
        );
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