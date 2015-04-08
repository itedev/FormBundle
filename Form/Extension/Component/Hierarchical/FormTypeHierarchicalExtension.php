<?php

namespace ITE\FormBundle\Form\Extension\Component\Hierarchical;

use ITE\FormBundle\SF\SFFormExtensionInterface;
use ITE\FormBundle\Util\FormUtils;
use ITE\JsBundle\SF\SFExtensionInterface;
use RuntimeException;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

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
     * @param SFFormExtensionInterface $sfForm
     */
    public function __construct(SFFormExtensionInterface $sfForm)
    {
        $this->sfForm = $sfForm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['hierarchical_parents']) || !is_callable($options['hierarchical_modifier'])) {
            return;
        }

//        $parents = $options['hierarchical_parents'];
//        $formModifier = $options['hierarchical_modifier'];
//
//        $propertyAccessor = PropertyAccess::createPropertyAccessor();
//        foreach ($parents as $parent) {
//            // $builder is parent form
//            $builder
//                ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($formModifier, $propertyAccessor, $parent) {
//                    $formModifier($event->getForm(), $propertyAccessor->getValue($event->getData(), $parent));
//                })
//            ;
//            $builder
//                ->get($parent)
//                ->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) use ($formModifier) {
//                    $formModifier($event->getForm()->getParent(), $event->getForm()->getData());
//                })
//            ;
//        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['hierarchical'] = !empty($options['hierarchical_parents']);
        if (empty($options['hierarchical_parents'])) {
            return;
        }

        foreach ($options['hierarchical_parents'] as $name) {
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
        $hierarchical = false;
        foreach ($view->children as $child) {
            // @todo: don't know why isset is needed? Same works for multipart option
            if (isset($child->vars['hierarchical']) && $child->vars['hierarchical']) {
                $hierarchical = true;
                break;
            }
        }

        $view->vars['hierarchical'] = $hierarchical;

        if (empty($options['hierarchical_parents'])) {
            return;
        }

        $parents = $options['hierarchical_parents'];

        $selector = FormUtils::generateSelector($view);

        $parentView = $view->parent;
        $parents = array_map(function($field) use ($parentView) {
            return $parentView->children[$field];
        }, $parents);
        $parentSelectors = array_map(function(FormView $parent) {
            return FormUtils::generateSelector($parent);
        }, $parents);

//        if (isset($view->vars['expanded']) && $view->vars['expanded']) {
//            $view->vars['attr']['data-property-path'] = $view->vars['full_name']
//                . (isset($view->vars['multiple']) && !empty($view->vars['multiple']) ? '[]' : '');
//        }

//        $elementOptions = isset($hierarchical['url'])
//            ? array('hierarchical_url' => $hierarchical['url'])
//            : array('hierarchical_callback' => $hierarchical['callback']);

        $elementOptions = [];
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $self = $this;

        $hierarchicalParentsNormalizer = function(Options $options, $hierarchicalParents) use ($self) {
            if (empty($hierarchicalParents)) {
                return false;
            }

            if (!is_array($hierarchicalParents)) {
                $hierarchicalParents = array($hierarchicalParents);
            }

            return $hierarchicalParents;
        };

        $resolver->setDefaults([
//            'hierarchical' => false,
            'hierarchical_parents' => null,
            'hierarchical_modifier' => null,
        ]);
        $resolver->setNormalizers(array(
            'hierarchical_parents' => $hierarchicalParentsNormalizer,
        ));
        $resolver->setAllowedTypes([
            'hierarchical_parents' => ['null', 'string', 'array'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
} 