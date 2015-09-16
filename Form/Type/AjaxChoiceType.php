<?php

namespace ITE\FormBundle\Form\Type;

use ITE\FormBundle\Form\ChoiceList\AjaxChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\ChoicesToValuesTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\ChoiceToValueTransformer;
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
        if ('choice' === $options['widget']) {
            // choice
            if ($options['multiple']) {
                $builder->addViewTransformer(new ChoicesToValuesTransformer($options['choice_list']));
            } else {
                $builder->addViewTransformer(new ChoiceToValueTransformer($options['choice_list']));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ('choice' === $options['widget']) {
            // choice
            $data = $form->getData();
            $empty = null === $data || [] === $data;
            if (!$empty) {
                $options['choice_list']->setData($data);
            }

            array_splice(
                $view->vars['block_prefixes'],
                array_search($this->getName(), $view->vars['block_prefixes']),
                0,
                [
                    'choice',
                ]
            );

            $view->vars = array_replace($view->vars, array(
                'multiple' => $options['multiple'],
                'expanded' => false,
                'preferred_choices' => $options['choice_list']->getPreferredViews(),
                'choices' => $options['choice_list']->getRemainingViews(),
                'separator' => '-------------------',
                'placeholder' => null,
            ));

            if ($options['multiple']) {
                $view->vars['is_selected'] = function ($choice, array $values) {
                    return in_array($choice, $values, true);
                };
            } else {
                $view->vars['is_selected'] = function ($choice, $value) {
                    return $choice === $value;
                };
            }

            $view->vars['placeholder_in_choices'] = 0 !== count($options['choice_list']->getChoicesForValues(['']));

            // Only add the empty value option if this is not the case
            if (null !== $options['placeholder'] && !$view->vars['placeholder_in_choices']) {
                $view->vars['placeholder'] = $options['placeholder'];
            }

            if ($options['multiple']) {
                $view->vars['full_name'] .= '[]';
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $self = $this;

        $choiceList = function (Options $options) {
            return new AjaxChoiceList();
        };

        $emptyData = function (Options $options) {
            if ($options['multiple']) {
                return [];
            }

            return '';
        };

        $placeholder = function (Options $options) {
            return $options['required'] ? null : '';
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

        $resolver->setDefaults([
            'multiple' => false,
            'choice_list' => $choiceList,
            'empty_data' => $emptyData,
            'placeholder' => $placeholder,
            'error_bubbling' => false,
            'compound' => false,
            'data_class' => null,
            'route' => null,
            'route_parameters' => [],
            'url' => null,
            'choice_label' => null,
            'separator' => ',',
            'widget' => 'choice',
        ]);
        $resolver->setNormalizers([
            'placeholder' => $placeholderNormalizer,
            'url' => $urlNormalizer,
        ]);
        $resolver->setAllowedValues([
            'widget' => [
                'choice',
                'hidden',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_ajax_choice';
    }
}
