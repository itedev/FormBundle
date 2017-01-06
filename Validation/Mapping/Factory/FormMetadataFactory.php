<?php

namespace ITE\FormBundle\Validation\Mapping\Factory;

use ITE\FormBundle\Util\FormUtils;
use ITE\FormBundle\Validation\ClientConstraint;
use ITE\FormBundle\Validation\ClientConstraintManagerInterface;
use ITE\FormBundle\Validation\Mapping\FormMetadata;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\ClassMetadata as ServerClassMetadata;
use ITE\FormBundle\Validation\Mapping\ClassMetadata as ClientClassMetadata;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Validator\Mapping\PropertyMetadata as ServerPropertyMetadata;
use ITE\FormBundle\Validation\Mapping\PropertyMetadata as ClientPropertyMetadata;
use Symfony\Component\Form\FormInterface;

/**
 * Class FormMetadataFactory
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormMetadataFactory implements FormMetadataFactoryInterface
{
    /**
     * @var MetadataFactoryInterface $serverMetadataFactory
     */
    protected $serverMetadataFactory;

    /**
     * @var MetadataFactoryInterface $clientMetadataFactory
     */
    protected $clientMetadataFactory;

    /**
     * @var ClientConstraintManagerInterface $clientConstraintManager
     */
    protected $clientConstraintManager;

    /**
     * @param MetadataFactoryInterface $serverMetadataFactory
     * @param MetadataFactoryInterface $clientMetadataFactory
     * @param ClientConstraintManagerInterface $clientConstraintManager
     */
    public function __construct(MetadataFactoryInterface $serverMetadataFactory,
        MetadataFactoryInterface $clientMetadataFactory, ClientConstraintManagerInterface $clientConstraintManager)
    {
        $this->serverMetadataFactory = $serverMetadataFactory;
        $this->clientMetadataFactory = $clientMetadataFactory;
        $this->clientConstraintManager = $clientConstraintManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFor(FormInterface $form, $constraintConversion = false)
    {
        $formMetadata = new FormMetadata();

        $clientConstraints = $this->getClientConstraints($form);
        if ($constraintConversion) {
            $constraints = $this->getConstraints($form);
            $clientConstraints = array_merge($clientConstraints, $this->convertConstraints($constraints));
        }

        $this->processConstraints($clientConstraints);
        $formMetadata->addConstraints($clientConstraints);

        return $formMetadata;
    }

    /**
     * @param FormInterface $form
     * @return array|Constraint[]
     */
    protected function getConstraints(FormInterface $form)
    {
        $constraints = [];
        if (null !== $dataClass = $form->getConfig()->getDataClass()) {
            // get server class constraints from data
            /** @var ServerClassMetadata $serverClassMetadata */
            $serverClassMetadata = $this->serverMetadataFactory->getMetadataFor($dataClass);
            $constraints = array_merge($constraints, $serverClassMetadata->getConstraints());
        } else {
            $mapped = $form->getConfig()->getMapped();
            $name = $form->getConfig()->getName();
            $parentForm = $form->getParent();
            if ($mapped
                && null !== $parentForm
                && null !== ($parentDataClass = $parentForm->getConfig()->getDataClass())) {
                // get server property constraints from data
                /** @var ServerClassMetadata $serverClassMetadata */
                $parentServerClassMetadata = $this->serverMetadataFactory->getMetadataFor($parentDataClass);
                if ($parentServerClassMetadata instanceof ServerClassMetadata) {
                    if ($parentServerClassMetadata->hasPropertyMetadata($name)) {
                        $propertyMetadatas = $parentServerClassMetadata->getPropertyMetadata($name);
                        foreach ($propertyMetadatas as $propertyMetadata) {
                            /** @var ServerPropertyMetadata $propertyMetadata */
                            $constraints = array_merge($constraints, $propertyMetadata->getConstraints());
                        }
                    }
                }
            }
        }

        // get server constraints from form
        $constraints = array_merge($constraints, $form->getConfig()->getOption('constraints'));

        return $constraints;
    }

    /**
     * @param FormInterface $form
     * @return array|ClientConstraint[]
     */
    protected function getClientConstraints(FormInterface $form)
    {
        $clientConstraints = [];
        if (null !== $dataClass = $form->getConfig()->getDataClass()) {
            // get client class constraints from data
            /** @var ClientClassMetadata $clientClassMetadata */
            $clientClassMetadata = $this->clientMetadataFactory->getMetadataFor($dataClass);
            $clientConstraints = array_merge($clientConstraints, $clientClassMetadata->getConstraints());
        } else {
            $mapped = $form->getConfig()->getMapped();
            $name = $form->getConfig()->getName();
            $parentForm = $form->getParent();
            if ($mapped
                && null !== $parentForm
                && null !== ($parentDataClass = $parentForm->getConfig()->getDataClass())) {
                // get client property constraints from data
                $parentClientClassMetadata = $this->clientMetadataFactory->getMetadataFor($parentDataClass);
                if ($parentClientClassMetadata instanceof ClientClassMetadata) {
                    if ($parentClientClassMetadata->hasPropertyMetadata($name)) {
                        $propertyMetadatas = $parentClientClassMetadata->getPropertyMetadata($name);
                        foreach ($propertyMetadatas as $propertyMetadata) {
                            /** @var ClientPropertyMetadata $propertyMetadata */
                            $clientConstraints = array_merge($clientConstraints, $propertyMetadata->getConstraints());
                        }
                    }
                }
            }
        }

        // get client constraints from form
        $clientConstraints = array_merge($clientConstraints, $form->getConfig()->getOption('client_constraints'));

        return $clientConstraints;
    }

    /**
     * @param array $constraints
     * @return array
     */
    protected function convertConstraints(array $constraints)
    {
        $clientConstraints = [];
        foreach ($constraints as $constraint) {
            $clientConstraint = $this->clientConstraintManager->convert($constraint);
            if ($clientConstraint) {
                $clientConstraints[] = $clientConstraint;
            }
        }

        return $clientConstraints;
    }

    /**
     * @param array $clientConstraints
     */
    protected function processConstraints(array $clientConstraints)
    {
        foreach ($clientConstraints as $clientConstraint) {
            $this->clientConstraintManager->process($clientConstraint);
        }
    }
}
