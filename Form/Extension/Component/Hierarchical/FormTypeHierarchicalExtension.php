<?php

namespace ITE\FormBundle\Form\Extension\Component\Hierarchical;

use ITE\FormBundle\FormAccess\FormAccess;
use ITE\FormBundle\FormAccess\FormAccessorInterface;
use ITE\FormBundle\SF\Form\ClientFormTypeExtensionInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\SFFormExtensionInterface;
use ITE\FormBundle\Util\HierarchicalUtils;
use ITE\JsBundle\SF\SFExtensionInterface;
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

/**
 * Class FormTypeHierarchicalExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeHierarchicalExtension extends AbstractTypeExtension implements ClientFormTypeExtensionInterface
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
     * @var FormAccessorInterface
     */
    protected $formAccessor;

    /**
     * @param SFFormExtensionInterface $sfForm
     * @param RequestStack $requestStack
     */
    public function __construct(SFFormExtensionInterface $sfForm, RequestStack $requestStack)
    {
        $this->sfForm = $sfForm;
        $this->requestStack = $requestStack;
        $this->formAccessor = FormAccess::createFormAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (
            ($request = $this->requestStack->getMasterRequest())
            && HierarchicalUtils::isHierarchicalRequest($request)
        ) {
            $originators = HierarchicalUtils::getOriginators($request);
            $builder->setAttribute('hierarchical_originator', $originators);

            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
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
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $hasHierarchicalParents = isset($options['hierarchical_parents']) && !empty($options['hierarchical_parents']);
        $isHierarchicalOriginator = isset($options['hierarchical_originator']) && $options['hierarchical_originator'];
        $isHierarchicalInteractive = !isset($options['hierarchical_interactive']) || $options['hierarchical_interactive'];

        if ($hasHierarchicalParents && $isHierarchicalInteractive) {
            $parentPaths = $options['hierarchical_parents'];
            $ascendantClientView = $clientView->getParent();
            $hierarchicalParents = [];
            foreach ($parentPaths as $parentPath) {
                $parentClientView = $this->formAccessor->getClientView($ascendantClientView, $parentPath);
                if (null === $parentClientView) {
                    throw new \RuntimeException(sprintf('Parent form "%s" is not found', $parentPath));
                }

                $hierarchicalChildren = $parentClientView->getOption('hierarchical_children', []);
                $hierarchicalChildren[] = $clientView->getOption('id');
                $parentClientView->setOption('hierarchical_children', $hierarchicalChildren);

                $hierarchicalParents[] = $parentClientView->getOption('id');
            }
            $clientView->setOption('hierarchical_parents', $hierarchicalParents);

            if (isset($options['hierarchical_trigger_event'])) {
                $clientView->setOption('hierarchical_trigger_event', $options['hierarchical_trigger_event']);
            }
            if (isset($options['hierarchical_changed'])) {
                $clientView->setOption('hierarchical_changed', $options['hierarchical_changed']);
            }
        }

        if ($isHierarchicalOriginator) {
            $clientView->setOption('hierarchical_originator', true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $hierarchicalParentsNormalizer = function (Options $options, $hierarchicalParents) {
            if (empty($hierarchicalParents)) {
                return null;
            }

            if (!is_array($hierarchicalParents)) {
                $hierarchicalParents = [$hierarchicalParents];
            }

            return $hierarchicalParents;
        };

        $resolver->setOptional([
            'hierarchical_parents',
            'hierarchical_callback',
            'hierarchical_originator',
            'hierarchical_trigger_event',
            'hierarchical_data',
            'hierarchical_changed',
            'hierarchical_interactive',
        ]);
        $resolver->setNormalizers([
            'hierarchical_parents' => $hierarchicalParentsNormalizer,
        ]);
        $resolver->setAllowedTypes([
            'hierarchical_parents' => ['string', 'array'],
            'hierarchical_callback' => ['callable'],
            'hierarchical_originator' => ['bool'],
            'hierarchical_trigger_event' => ['string'],
            'hierarchical_changed' => ['bool'],
            'hierarchical_interactive' => ['bool'],
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
