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
        $url = function (Options $options) use ($self) {
            return $self->getRouter()->generate($options['route'], $options['route_parameters']);
        };

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

        $resolver->setDefaults(array(
            'multiple' => false,
            'empty_value' => $emptyValue,
            'empty_data' => $emptyData,
            'compound' => false,
            'error_bubbling' => true,
            'choice_label' => 'Previously selected',
            'choice_label_builder' => null,
            'route_parameters' => array(),
            'url' => $url,
        ));

        $resolver->setAllowedTypes(array(
            'choice_label' => array('string', 'function')
        ));

        $resolver->setRequired(array(
            'route',
        ));

        $resolver->setNormalizers(array(
            'empty_value' => $emptyValueNormalizer,
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