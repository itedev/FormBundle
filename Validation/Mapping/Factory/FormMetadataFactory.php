<?php

namespace ITE\FormBundle\Validation\Mapping\Factory;

use ITE\FormBundle\Util\FormUtils;
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
class FormMetadataFactory
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
     * @param MetadataFactoryInterface $serverMetadataFactory
     * @param MetadataFactoryInterface $clientMetadataFactory
     */
    public function __construct(MetadataFactoryInterface $serverMetadataFactory, MetadataFactoryInterface $clientMetadataFactory)
    {
        $this->serverMetadataFactory = $serverMetadataFactory;
        $this->clientMetadataFactory = $clientMetadataFactory;
    }

    /**
     * @param FormInterface $form
     * @return FormMetadata
     */
    public function getMetadataFor(FormInterface $form)
    {
        if (!$form->isRoot()) {
            // error
        }

        $formMetadata = new FormMetadata();

        $serverClassMetadata = null;
        $clientClassMetadata = null;
        if (null !== $dataClass = $form->getConfig()->getDataClass()) {
            // get server class constraints from data
            /** @var ServerClassMetadata $serverClassMetadata */
            $serverClassMetadata = $this->serverMetadataFactory->getMetadataFor($dataClass);
            $formMetadata->addServerConstraints($serverClassMetadata->getConstraints());

            // get client class constraints from data
            /** @var ClientClassMetadata $clientClassMetadata */
            $clientClassMetadata = $this->clientMetadataFactory->getMetadataFor($dataClass);
            $formMetadata->addClientConstraints($clientClassMetadata->getConstraints());
        }
        // get server constraints from form
        $formMetadata->addServerConstraints($form->getConfig()->getOption('constraints'));

        // get client constraints from form
        $formMetadata->addClientConstraints($form->getConfig()->getOption('client_constraints'));

        $serverMetadataFactory = $this->serverMetadataFactory;
        $clientMetadataFactory = $this->clientMetadataFactory;
        FormUtils::formWalkRecursiveWithPrototype($form, function(FormInterface $child,
            FormMetadata $parentFormMetadata, ServerClassMetadata $parentServerClassMetadata = null,
            ClientClassMetadata $parentClientClassMetadata = null) use ($serverMetadataFactory, $clientMetadataFactory) {
            $name = $child->getName();

            $formMetadata = new FormMetadata();

            $serverClassMetadata = null;
            $clientClassMetadata = null;
            $mapped = $child->getConfig()->getMapped();
            if ($mapped) {
                if (null !== $dataClass = $child->getConfig()->getDataClass()) {
                    // get server class constraints from data
                    /** @var ServerClassMetadata $serverClassMetadata */
                    $serverClassMetadata = $serverMetadataFactory->getMetadataFor($dataClass);
                    $formMetadata->addServerConstraints($serverClassMetadata->getConstraints());

                    // get client class constraints from data
                    /** @var ClientClassMetadata $clientClassMetadata */
                    $clientClassMetadata = $clientMetadataFactory->getMetadataFor($dataClass);
                    $formMetadata->addClientConstraints($clientClassMetadata->getConstraints());
                }

                // get server property constraints from data
                if ($parentServerClassMetadata instanceof ServerClassMetadata) {
                    if ($parentServerClassMetadata->hasPropertyMetadata($name)) {
                        $propertyMetadatas = $parentServerClassMetadata->getPropertyMetadata($name);
                        foreach ($propertyMetadatas as $propertyMetadata) {
                            /** @var ServerPropertyMetadata $propertyMetadata */
                            $formMetadata->addServerConstraints($propertyMetadata->getConstraints());
                        }
                    }
                }

                // get client property constraints from data
                if ($parentClientClassMetadata instanceof ClientClassMetadata) {
                    if ($parentClientClassMetadata->hasPropertyMetadata($name)) {
                        $propertyMetadatas = $parentClientClassMetadata->getPropertyMetadata($name);
                        foreach ($propertyMetadatas as $propertyMetadata) {
                            /** @var ClientPropertyMetadata $propertyMetadata */
                            $formMetadata->addClientConstraints($propertyMetadata->getConstraints());
                        }
                    }
                }
            }

            // get server constraints from form
            $formMetadata->addServerConstraints($child->getConfig()->getOption('constraints'));

            // get client constraints from form
            $formMetadata->addClientConstraints($child->getConfig()->getOption('client_constraints'));

            $parentFormMetadata->addChild($child->getName(), $formMetadata);

            return [$formMetadata, $serverClassMetadata, $clientClassMetadata];
        }, [$formMetadata, $serverClassMetadata, $clientClassMetadata]);

        return $formMetadata;
    }
}