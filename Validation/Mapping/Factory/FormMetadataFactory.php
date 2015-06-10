<?php

namespace ITE\FormBundle\Validation\Mapping\Factory;

use ITE\FormBundle\Util\FormUtils;
use ITE\FormBundle\Validation\ConstraintManagerInterface;
use ITE\FormBundle\Validation\Mapping\FormMetadata;
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
     * @var ConstraintManagerInterface $constraintManager
     */
    protected $constraintManager;

    /**
     * @param MetadataFactoryInterface $serverMetadataFactory
     * @param MetadataFactoryInterface $clientMetadataFactory
     * @param ConstraintManagerInterface $constraintManager
     */
    public function __construct(MetadataFactoryInterface $serverMetadataFactory,
        MetadataFactoryInterface $clientMetadataFactory, ConstraintManagerInterface $constraintManager)
    {
        $this->serverMetadataFactory = $serverMetadataFactory;
        $this->clientMetadataFactory = $clientMetadataFactory;
        $this->constraintManager = $constraintManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFor(FormInterface $form, $constraintConversion = false)
    {
        $formMetadata = new FormMetadata();

        if (null !== $dataClass = $form->getConfig()->getDataClass()) {
            if ($constraintConversion) {
                // get server class constraints from data
                /** @var ServerClassMetadata $serverClassMetadata */
                $serverClassMetadata = $this->serverMetadataFactory->getMetadataFor($dataClass);
                $formMetadata->addConstraints($this->convertConstraints($serverClassMetadata->getConstraints()));
            }

            // get client class constraints from data
            /** @var ClientClassMetadata $clientClassMetadata */
            $clientClassMetadata = $this->clientMetadataFactory->getMetadataFor($dataClass);
            $formMetadata->addConstraints($clientClassMetadata->getConstraints());
        } else {
            $mapped = $form->getConfig()->getMapped();
            $name = $form->getConfig()->getName();
            $parentForm = $form->getParent();
            if ($mapped
                && null !== $parentForm
                && null !== ($parentDataClass = $parentForm->getConfig()->getDataClass())) {
                if ($constraintConversion) {
                    // get server property constraints from data
                    /** @var ServerClassMetadata $serverClassMetadata */
                    $parentServerClassMetadata = $this->serverMetadataFactory->getMetadataFor($parentDataClass);
                    if ($parentServerClassMetadata instanceof ServerClassMetadata) {
                        if ($parentServerClassMetadata->hasPropertyMetadata($name)) {
                            $propertyMetadatas = $parentServerClassMetadata->getPropertyMetadata($name);
                            foreach ($propertyMetadatas as $propertyMetadata) {
                                /** @var ServerPropertyMetadata $propertyMetadata */
                                $formMetadata->addConstraints($this->convertConstraints($propertyMetadata->getConstraints()));
                            }
                        }
                    }
                }

                // get client property constraints from data
                $parentClientClassMetadata = $this->clientMetadataFactory->getMetadataFor($parentDataClass);
                if ($parentClientClassMetadata instanceof ClientClassMetadata) {
                    if ($parentClientClassMetadata->hasPropertyMetadata($name)) {
                        $propertyMetadatas = $parentClientClassMetadata->getPropertyMetadata($name);
                        foreach ($propertyMetadatas as $propertyMetadata) {
                            /** @var ClientPropertyMetadata $propertyMetadata */
                            $formMetadata->addConstraints($propertyMetadata->getConstraints());
                        }
                    }
                }
            }
        }

        if ($constraintConversion) {
            // get server constraints from form
            $formMetadata->addConstraints($this->convertConstraints($form->getConfig()->getOption('constraints')));
        }

        // get client constraints from form
        $formMetadata->addConstraints($form->getConfig()->getOption('client_constraints'));

        return $formMetadata;
    }

    /**
     * @param array $constraints
     * @return array
     */
    protected function convertConstraints(array $constraints)
    {
        $clientConstraints = [];
        foreach ($constraints as $constraint) {
            $clientConstraint = $this->constraintManager->convert($constraint);
            if ($clientConstraint) {
                $clientConstraints[] = $clientConstraint;
            }
        }

        return $clientConstraints;
    }
}