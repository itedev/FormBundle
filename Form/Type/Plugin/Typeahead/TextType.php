<?php

namespace ITE\FormBundle\Form\Type\Plugin\Typeahead;

use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\TypeaheadPlugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class TextType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TextType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * @var array
     */
    protected $datasetOptions;

    /**
     * @var array
     */
    protected $engineOptions;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @param array $options
     * @param array $datasetOptions
     * @param array $engineOptions
     * @param RouterInterface $router
     */
    public function __construct(array $options, array $datasetOptions, array $engineOptions, RouterInterface $router)
    {
        parent::__construct($options);
        $this->datasetOptions = $datasetOptions;
        $this->engineOptions = $engineOptions;
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
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $prefetchOptions = null;
        if ($options['prefetch_url']) {
            $prefetchOptions = [
                'url' => $options['prefetch_url'],
            ];
        }

        $remoteOptions = null;
        if ($options['remote_url']) {
            $remoteOptions = [
                'url' => $options['remote_url'],
            ];
        }

        $clientView->addPlugin(TypeaheadPlugin::getName(), [
            'extras' => (object) [],
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options']),
            'dataset_options' => (object) array_replace_recursive($this->datasetOptions, $options['dataset_options']),
            'engine_options' => (object) array_replace_recursive($this->engineOptions, $options['engine_options'], [
                'prefetch' => $prefetchOptions,
                'remote' => $remoteOptions,
            ]),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $self = $this;
        $prefetchUrlNormalizer = function (Options $options, $prefetchUrl) use ($self) {
            if (!empty($options['prefetch_route'])) {
                return $self->getRouter()->generate($options['prefetch_route'], $options['prefetch_route_parameters']);
            }

            return $prefetchUrl;
        };
        $remoteUrlNormalizer = function (Options $options, $remoteUrl) use ($self) {
            if (!empty($options['remote_route'])) {
                return $self->getRouter()->generate($options['remote_route'], $options['remote_route_parameters']);
            }

            return $remoteUrl;
        };

        parent::setDefaultOptions($resolver);
        $resolver->setDefaults([
            'dataset_options' => [],
            'engine_options' => [],
            'prefetch_route' => null,
            'prefetch_route_parameters' => [],
            'prefetch_url' => null,
            'remote_route' => null,
            'remote_route_parameters' => [],
            'remote_url' => null,
        ]);
        $resolver->setNormalizers([
            'prefetch_url' => $prefetchUrlNormalizer,
            'remote_url' => $remoteUrlNormalizer,
        ]);
        $resolver->setAllowedTypes([
            'dataset_options' => ['array'],
            'engine_options' => ['array'],
        ]);
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
        return 'ite_typeahead_text';
    }
}
