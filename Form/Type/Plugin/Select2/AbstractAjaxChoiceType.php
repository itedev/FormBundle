<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AbstractAjaxChoiceType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractAjaxChoiceType extends AbstractType implements ClientFormTypeInterface
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
        $self = $this;

        $urlNormalizer = function (Options $options, $url) use ($self) {
            if (!empty($options['route'])) {
                return $self->getRouter()->generate($options['route'], $options['route_parameters']);
            } elseif (!empty($url)) {
                return $url;
            } else {
                throw new \RuntimeException('You must specify "route" or "url" option.');
            }
        };
        $resolver->setDefaults([
            'choices' => [],
            'allow_modify' => true,
            'plugin_options' => [],
            'route' => null,
            'route_parameters' => [],
            'url' => null,
        ]);
        $resolver->setNormalizers([
            'url' => $urlNormalizer,
        ]);
        $resolver->setAllowedTypes([
            'plugin_options' => ['array'],
        ]);
        $resolver->setAllowedValues([
            'allow_modify' => [true],
            'choices' => [[]],
            'expanded' => [false],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['plugins'][Select2Plugin::getName()] = [
            'extras' => [
                'ajax' => true,
            ],
            'options' => array_replace_recursive($this->options, $options['plugin_options'], [
                'ajax' => [
                    'url' => $options['url'],
                    'dataType' => 'json',
                ],
                'multiple' => $options['multiple'],
                'placeholder' => $options['placeholder'],
                'allowClear' => !$options['required'],
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->setOption('plugins', [
            Select2Plugin::getName() => [
                'extras' => [
                    'ajax' => true,
                ],
                'options' => array_replace_recursive($this->options, $options['plugin_options'], [
                    'ajax' => [
                        'url' => $options['url'],
                        'dataType' => 'json',
                    ],
                    'multiple' => $options['multiple'],
                    'placeholder' => $options['placeholder'],
                    'allowClear' => !$options['required'],
                ]),
            ]
        ]);
    }

}