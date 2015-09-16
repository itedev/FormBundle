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
     * @param array $options
     * @param RouterInterface $router
     */
    public function __construct(array $options, RouterInterface $router)
    {
        $this->router = $router;
        parent::__construct($options);
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
        $clientView->setOption('plugins', [
            TypeaheadPlugin::getName() => [
                'extras' => (object) [],
                'options' => (object) array_replace_recursive($this->options, $options['plugin_options']),
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $self = $this;
        $prefetchUrlNormalizer = function (Options $options, $url) use ($self) {
            if (!empty($options['prefetch_route'])) {
                return $self->getRouter()->generate($options['prefetch_route'], $options['prefetch_route_parameters']);
            } elseif (!empty($url)) {
                return $url;
            } else {
                throw new \RuntimeException('You must specify "prefetch_route" or "prefetch_url" option.');
            }
        };
        $remoteUrlNormalizer = function (Options $options, $url) use ($self) {
            if (!empty($options['remote_route'])) {
                return $self->getRouter()->generate($options['remote_route'], $options['remote_route_parameters']);
            } elseif (!empty($url)) {
                return $url;
            } else {
                throw new \RuntimeException('You must specify "remote_route" or "remote_url" option.');
            }
        };

        parent::setDefaultOptions($resolver);
        $resolver->setDefaults([
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
