<?php

namespace ITE\FormBundle\Form\Type;

use ITE\FormBundle\Util\UrlUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class FileUploadFileType
 * @package ITE\FormBundle\Form\Type
 */
class FileUploadFileType extends AbstractType
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
     * @param $options
     * @param RouterInterface $router
     */
    public function __construct($options, RouterInterface $router)
    {
        $this->options = $options;
        $this->router = $router;
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
                'route_parameters' => array(),
                'url' => $url,
                'extras' => array(),
                'plugin_options' => array(),
            ));
        $resolver->setAllowedTypes(array(
                'extras' => array('array'),
                'plugin_options' => array('array'),
            ));
        $resolver->setRequired(array(
                'route',
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $root = $this->getRootView($view);
        $url = $options['url'];
        if ($root->vars['ajax_token']) {
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
                    'filesContainer' => '#' . $view->vars['id'] . '_files',
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