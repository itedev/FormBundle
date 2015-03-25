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
 * @package FormBundle\Form\Type
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

        $emptyValueNormalizer = function (Options $options, $emptyValue) {
            if ($options['multiple']) {
                // never use an empty value for this case
                return;
            } elseif (false === $emptyValue) {
                // an empty value should be added but the user decided otherwise
                return;
            }

            // empty value has been set explicitly
            return $emptyValue;
        };

        $urlNormalizer = function (Options $options, $url) use ($self) {
            if (isset($options['route'])) {
                return $self->getRouter()->generate($options['route'], $options['route_parameters']);
            } elseif (isset($url)) {
                return $url;
            } else {
                throw new \RuntimeException('You must specify "route" or "url" option.');
            }
        };

        $resolver->setDefaults(array(
            'multiple' => false,
            'empty_value' => $emptyValue,
            'empty_data' => $emptyData,
            'compound' => false,
            'error_bubbling' => true,
            'route' => null,
            'route_parameters' => array(),
            'url' => null,
            'choice_label' => null,
        ));

        $resolver->setAllowedTypes(array(
            'choice_label' => array('string', 'function', 'null')
        ));

        $resolver->setNormalizers(array(
            'empty_value' => $emptyValueNormalizer,
            'url' => $urlNormalizer,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
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