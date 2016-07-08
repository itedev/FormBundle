<?php

namespace ITE\FormBundle\Form\Extension\Component\Collection;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CollectionTypeCollectionExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CollectionTypeCollectionExtension extends AbstractTypeExtension
{
    /**
     * @var array $widgetShowAnimation
     */
    protected $widgetShowAnimation;

    /**
     * @var array $widgetHideAnimation
     */
    protected $widgetHideAnimation;

    /**
     * @param array $widgetShowAnimation
     * @param array $widgetHideAnimation
     */
    public function __construct($widgetShowAnimation, $widgetHideAnimation)
    {
        $this->widgetShowAnimation = $widgetShowAnimation;
        $this->widgetHideAnimation = $widgetHideAnimation;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['collection_id'] = isset($options['collection_id'])
            ? $options['collection_id']
            : $view->vars['unique_block_prefix'];
        $view->vars['collection_item_tag'] = $options['collection_item_tag'];

        $view->vars['attr']['data-collection-id'] = $view->vars['collection_id'];
        $view->vars['attr']['data-show-animation'] = json_encode($options['widget_show_animation']);
        $view->vars['attr']['data-hide-animation'] = json_encode($options['widget_hide_animation']);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (!is_callable($options['prototype_data']) || !$form->getConfig()->hasAttribute('prototype')) {
            return;
        }

        $data = call_user_func($options['prototype_data'], $form);

        /** @var FormInterface $oldPrototype */
        $oldPrototype = $form->getConfig()->getAttribute('prototype');
        $oldPrototypeOptions = $oldPrototype->getConfig()->getOption('original_options');

        $factory = $form->getConfig()->getFormFactory();
        $prototype = $factory->createNamed($options['prototype_name'], $options['type'], $data, $oldPrototypeOptions);

        $form->setRawAttribute('prototype', $prototype);
        $view->vars['prototype'] = $prototype->createView($view);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $globalWidgetShowAnimation = $this->widgetShowAnimation;
        $widgetShowAnimationNormalizer = function (Options $options, $widgetShowAnimation) use ($globalWidgetShowAnimation) {
            if (!is_array($widgetShowAnimation)) {
                throw new \InvalidArgumentException('The "widget_show_animation" option must be an "array".');
            }
            if (!isset($widgetShowAnimation['type'])
                || !is_string($widgetShowAnimation['type'])
                || empty($widgetShowAnimation['type'])) {
                $widgetShowAnimation['type'] = $globalWidgetShowAnimation['type'];
            }
            if (!isset($widgetShowAnimation['length']) || !is_int($widgetShowAnimation['length'])) {
                $widgetShowAnimation['length'] = $globalWidgetShowAnimation['length'];
            }

            return $widgetShowAnimation;
        };

        $globalWidgetHideAnimation = $this->widgetHideAnimation;
        $widgetHideAnimationNormalizer = function (Options $options, $widgetHideAnimation) use ($globalWidgetHideAnimation) {
            if (!is_array($widgetHideAnimation)) {
                throw new \InvalidArgumentException('The "widget_hide_animation" option must be an "array".');
            }
            if (!isset($widgetHideAnimation['type'])
                || !is_string($widgetHideAnimation['type'])
                || empty($widgetHideAnimation['type'])) {
                $widgetHideAnimation['type'] = $globalWidgetHideAnimation['type'];
            }
            if (!isset($widgetHideAnimation['length']) || !is_int($widgetHideAnimation['length'])) {
                $widgetHideAnimation['length'] = $globalWidgetHideAnimation['length'];
            }

            return $widgetHideAnimation;
        };

        $resolver->setDefaults([
            'collection_id' => null,
            'collection_item_tag' => 'div',
            'widget_show_animation' => $this->widgetShowAnimation,
            'widget_hide_animation' => $this->widgetHideAnimation,
            'prototype_data' => null,
        ]);
        $resolver->setNormalizers([
            'widget_show_animation' => $widgetShowAnimationNormalizer,
            'widget_hide_animation' => $widgetHideAnimationNormalizer,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'collection';
    }
}
