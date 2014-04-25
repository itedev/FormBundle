<?php

namespace ITE\FormBundle\Form\Extension\Component\Hierarchical;

use ITE\FormBundle\Util\FormUtils;
use ITE\JsBundle\SF\SFExtensionInterface;
use RuntimeException;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class FormTypeHierarchicalExtension
 * @package ITE\FormBundle\Form\Extension\Component\Hierarchical
 */
class FormTypeHierarchicalExtension extends AbstractTypeExtension
{
    /**
     * @var SFExtensionInterface $sfForm
     */
    protected $sfForm;

    /**
     * @param SFExtensionInterface $sfForm
     * @param RouterInterface $router
     */
    public function __construct(SFExtensionInterface $sfForm, RouterInterface $router)
    {
        $this->sfForm = $sfForm;
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

        $hierarchicalNormalizer = function(Options $options, $hierarchical) use ($type) {
            if (empty($hierarchical)) {
                return array();
            }

            if (!isset($hierarchical['parents']) || empty($hierarchical['parents'])) {
                throw new RuntimeException(sprintf('Missing "%s" sub-option inside "%s" option.',
                    'parents', 'hierarchical'));
            }

            if ((!isset($hierarchical['route']) || empty($hierarchical['route']))
                && (!isset($hierarchical['callback']) || empty($hierarchical['callback']))) {
                throw new RuntimeException(sprintf('You must specify either "%s" or "%s" sub-option inside ' .
                    '"%s" option.', 'route', 'callback', 'hierarchical'));
            }

            $parents = $hierarchical['parents'];
            if (!is_array($parents) && !$parents instanceof \Traversable) {
                $parents = array($parents);
            }

            $normalizedValue = array(
                'parents' => $parents,
            );

            if (isset($hierarchical['route'])) {
                $routeParameters = isset($hierarchical['route_parameters']) && is_array($hierarchical['route_parameters'])
                    ? $hierarchical['route_parameters']
                    : array();
                $normalizedValue['url'] = $type->getRouter()->generate($hierarchical['route'], $routeParameters);
            } else {
                $normalizedValue['callback'] = $hierarchical['callback'];
            }

            return $normalizedValue;
        };

        $resolver->setDefaults(array(
            'hierarchical' => array(),
        ));
        $resolver->setNormalizers(array(
            'hierarchical' => $hierarchicalNormalizer,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (empty($options['hierarchical'])) {
            return;
        }

        foreach ($options['hierarchical']['parents'] as $name) {
            if (!isset($view->parent->children[$name])) {
                throw new RuntimeException(sprintf('Child "%s" does not exist.', $name));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (empty($options['hierarchical'])) {
            return;
        }

        $hierarchical = $options['hierarchical'];
        $selector = FormUtils::generateSelector($view);

        $parentView = $view->parent;
        $parents = array_map(function($field) use ($parentView) {
            return $parentView->children[$field];
        }, $hierarchical['parents']);
        $parentSelectors = array_map(function(FormView $parent) {
            return FormUtils::generateSelector($parent);
        }, $parents);

        if (isset($view->vars['expanded']) && $view->vars['expanded']) {
            $view->vars['attr']['data-property-path'] = $view->vars['full_name']
                . (isset($view->vars['multiple']) && !empty($view->vars['multiple']) ? '[]' : '');
        }

        $elementOptions = isset($hierarchical['url'])
            ? array('hierarchical_url' => $hierarchical['url'])
            : array('hierarchical_callback' => $hierarchical['callback']);

        if (1 === count($parents)
            && FormUtils::isFormTypeChildOf($form, 'choice')
            && isset($options['choices'])
            && empty($options['choices'])) {
            /** @var $firstParent FormView */
            $firstParent = reset($parents);
            if (FormUtils::isFormViewContainBlockPrefix($firstParent, 'choice')) {
                $elementOptions['hierarchical_auto_initialize'] = true;
            }
        }

        $this->sfForm->getElementBag()->addHierarchicalElement($selector, $parentSelectors, $elementOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
} 