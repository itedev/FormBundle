<?php

namespace ITE\FormBundle\Form\Extension\Component\Hierarchical;

use ITE\FormBundle\SF\SFFormExtensionInterface;
use ITE\FormBundle\Util\FormUtils;
use ITE\JsBundle\SF\SFExtensionInterface;
use RuntimeException;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Class FormTypeHierarchicalExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeHierarchicalExtension extends AbstractTypeExtension
{
    /**
     * @var SFExtensionInterface $sfForm
     */
    protected $sfForm;

    /**
     * @var RequestStack $requestStack
     */
    protected $requestStack;

    /**
     * @param SFFormExtensionInterface $sfForm
     * @param RequestStack $requestStack
     */
    public function __construct(SFFormExtensionInterface $sfForm, RequestStack $requestStack)
    {
        $this->sfForm = $sfForm;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = $this->requestStack->getMasterRequest();
        if ($request->headers->has('X-SF-Hierarchical')) {
            $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
                $form = $event->getForm();
                if (!$form->isRoot()) {
                    return;
                }

                $event->stopPropagation(); // prevent form validation
                $form->addError(new FormError('hierarchical')); // dummy error to suppress successful $form->isValid() call
            }, 900);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (empty($options['hierarchical_parents'])) {
            return;
        }

        $selector = FormUtils::generateSelector($view);

        $parents = $options['hierarchical_parents'];
        $ascendantView = $view->parent;
        $parentViews = array_map(function($parent) use ($ascendantView) {
            return $ascendantView->children[$parent];
        }, $parents);
        $parentSelectors = array_map(function(FormView $parentView) {
            return FormUtils::generateSelector($parentView);
        }, $parentViews);

        $elementOptions = [];
//        if (1 === count($parentViews)
//            && FormUtils::isFormTypeChildOf($form, 'choice')
//            && isset($options['choices'])
//            && empty($options['choices'])) {
//            /** @var $firstParent FormView */
//            $firstParentView = reset($parentViews);
//            if (FormUtils::isFormViewContainBlockPrefix($firstParentView, 'choice')) {
//                $elementOptions['hierarchical_auto_initialize'] = true;
//            }
//        }

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
                return null;
            }

            if (!is_array($hierarchicalParents)) {
                $hierarchicalParents = array($hierarchicalParents);
            }

            return $hierarchicalParents;
        };

        $resolver->setDefaults([
            'hierarchical_parents' => null,
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