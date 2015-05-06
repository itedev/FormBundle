<?php

namespace ITE\FormBundle\Validation;

use ITE\FormBundle\Util\FormUtils;
use ITE\FormBundle\Validation\Mapping\FormMetadata;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Symfony\Component\Form\FormInterface;

/**
 * Class FormMetadataFactory
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormMetadataFactory
{
    /**
     * @var MetadataFactoryInterface $metadataFactory
     */
    protected $metadataFactory;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
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

        // get constraints from data
        $classMetadata = null;
        if (null !== $dataClass = $form->getConfig()->getDataClass()) {
            /** @var ClassMetadata $classMetadata */
            $classMetadata = $this->metadataFactory->getMetadataFor($dataClass);
            $formMetadata->addConstraints($classMetadata->getConstraints());
        }
        // get constraints from form
        $constraints = $form->getConfig()->getOption('constraints');
        $formMetadata->addConstraints($constraints);

        $metadataFactory = $this->metadataFactory;
        FormUtils::formWalkRecursiveWithPrototype($form, function(FormInterface $child,
            FormMetadata $parentFormMetadata, ClassMetadata $parentClassMetadata = null) use ($metadataFactory) {
            $name = $child->getName();

            $formMetadata = new FormMetadata();

            $classMetadata = null;
            $mapped = $child->getConfig()->getMapped();
            if ($mapped) {
                if (null !== $dataClass = $child->getConfig()->getDataClass()) {
                    /** @var ClassMetadata $classMetadata */
                    $classMetadata = $metadataFactory->getMetadataFor($dataClass);
                    $formMetadata->addConstraints($classMetadata->getConstraints());
                }

                if ($parentClassMetadata instanceof ClassMetadata) {
                    if ($parentClassMetadata->hasPropertyMetadata($name)) {
                        $propertyMetadatas = $parentClassMetadata->getPropertyMetadata($name);
                        foreach ($propertyMetadatas as $propertyMetadata) {
                            /** @var PropertyMetadata $propertyMetadata */
                            $formMetadata->addConstraints($propertyMetadata->getConstraints());
                        }
                    }
                }
            }

            // get constraints from form
            $constraints = $child->getConfig()->getOption('constraints');
            $formMetadata->addConstraints($constraints);

            $parentFormMetadata->add($child->getName(), $formMetadata);

            return [$formMetadata, $classMetadata];
        }, [$formMetadata, $classMetadata]);

        return $formMetadata;
    }
}