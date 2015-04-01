<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AbstractAjaxChoiceType
 * @package ITE\FormBundle\Form\Type\Plugin\Select2
 */
abstract class AbstractAjaxChoiceType extends AbstractType
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
        $resolver->setDefaults(array(
            'choices' => array(),
            'allow_modify' => true,
            'plugin_options' => array(),
            'route' => null,
            'route_parameters' => array(),
            'url' => null,
        ));
        $resolver->setNormalizers(array(
            'url' => $urlNormalizer,
        ));
        $resolver->setAllowedTypes(array(
            'plugin_options' => array('array'),
        ));
        $resolver->setAllowedValues(array(
            'allow_modify' => array(true),
            'choices' => array(array()),
            'expanded' => array(false),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = array();
        }
        $view->vars['plugins'][Select2Plugin::getName()] = array(
            'extras' => array(
                'ajax' => true,
            ),
            'options' => array_replace_recursive($this->options, $options['plugin_options'], array(
                'ajax' => array(
                    'url' => $options['url'],
                    'dataType' => 'json',
                ),
                'multiple' => $options['multiple'],
                'allowClear' => false !== $options['empty_value'] && null !== $options['empty_value'],
            ))
        );
    }

}