<?php

namespace ITE\FormBundle\Form\Extension\Component\Validation;

use ITE\FormBundle\SF\Form\ClientFormTypeExtensionInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\Validation\ClientConstraint;
use ITE\FormBundle\Validation\ClientConstraintManagerInterface;
use ITE\FormBundle\Validation\Mapping\Factory\FormMetadataFactoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FormTypeValidationExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeValidationExtension extends AbstractTypeExtension implements ClientFormTypeExtensionInterface
{
    /**
     * @var FormMetadataFactoryInterface $metadataFactory
     */
    protected $metadataFactory;

    /**
     * @var ClientConstraintManagerInterface
     */
    protected $clientConstraintManager;

    /**
     * @param FormMetadataFactoryInterface $metadataFactory
     * @param ClientConstraintManagerInterface $clientConstraintManager
     */
    public function __construct(
        FormMetadataFactoryInterface $metadataFactory,
        ClientConstraintManagerInterface $clientConstraintManager
    ) {
        $this->metadataFactory = $metadataFactory;
        $this->clientConstraintManager = $clientConstraintManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $constraintsNormalizer = function (Options $options, $constraints) {
            return is_object($constraints) ? [$constraints] : (array) $constraints;
        };

        $resolver->setOptional([
            'client_validation',
            'constraint_conversion',
        ]);
        $resolver->setDefaults([
            'client_constraints' => [],
        ]);
        $resolver->setNormalizers([
            'client_constraints' => $constraintsNormalizer,
        ]);
        $resolver->setAllowedTypes([
            'client_validation' => ['string'],
            'constraint_conversion' => ['bool'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        if ($clientView->isRoot()) {
            if (isset($options['client_validation'])) {
                $clientView->setAttribute('client_validation', $options['client_validation']);

                if (isset($options['constraint_conversion'])) {
                    $clientView->setAttribute('constraint_conversion', $options['constraint_conversion']);
                }
            }
        }

        $rootClientView = $clientView->getRoot();
        $clientValidation = $rootClientView->getAttribute('client_validation', null);
        if ($clientValidation) {
            $constraintConversion = $rootClientView->getAttribute('constraint_conversion', false);
            $formMetadata = $this->metadataFactory->getMetadataFor($form, $constraintConversion);
            $constraints = $formMetadata->getConstraints();
            if (!empty($constraints)) {
                $clientView->setOption('constraints', $constraints);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
