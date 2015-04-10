<?php

namespace ITE\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AjaxChoiceType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxChoiceType extends AbstractType
{
    /**
     * @var RouterInterface $router
     */
    protected $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $self = $this;

        $emptyData = function (Options $options) {
            if ($options['multiple']) {
                return array();
            }

            return '';
        };

        $emptyValue = function (Options $options) {
            return $options['required'] ? null : '';
        };

        // for BC with the "empty_value" option
        $placeholder = function (Options $options) {
            return $options['empty_value'];
        };

        $placeholderNormalizer = function (Options $options, $placeholder) {
            if ($options['multiple']) {
                // never use an empty value for this case
                return;
            } elseif (false === $placeholder) {
                // an empty value should be added but the user decided otherwise
                return;
            }

            // empty value has been set explicitly
            return $placeholder;
        };

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
            'multiple' => false,
            'empty_data' => $emptyData,
            'empty_value' => $emptyValue,
            'placeholder' => $placeholder,
            'error_bubbling' => false,
            'route' => null,
            'route_parameters' => array(),
            'url' => null,
            'choice_label' => null,
        ));

        $resolver->setNormalizers(array(
            'empty_value' => $placeholderNormalizer,
            'placeholder' => $placeholderNormalizer,
            'url' => $urlNormalizer,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, array(
            'multiple' => $options['multiple'],
            'placeholder' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_ajax_choice';
    }
} 