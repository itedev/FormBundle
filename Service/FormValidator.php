<?php

namespace ITE\FormBundle\Service;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ExecutionContext;
use Symfony\Component\Validator\Mapping\PropertyMetadata;
use Symfony\Component\Validator\MetadataFactoryInterface;
use Symfony\Component\Validator\ValidationVisitor;

/**
 * Class FormValidator
 * @package ITE\FormBundle\Service
 */
class FormValidator
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var ConstraintValidatorFactoryInterface
     */
    private $validatorFactory;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var null|string
     */
    private $translationDomain;

    /**
     * @var array
     */
    private $objectInitializers;

    public function __construct(
        MetadataFactoryInterface $metadataFactory,
        ConstraintValidatorFactoryInterface $validatorFactory,
        TranslatorInterface $translator,
        $translationDomain = 'validators',
        array $objectInitializers = array()
    )
    {
        $this->metadataFactory = $metadataFactory;
        $this->validatorFactory = $validatorFactory;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->objectInitializers = $objectInitializers;
    }

    /**
     * {@inheritDoc}
     */
    public function getRules($value, $groups = null, $traverse = false, $deep = false)
    {
        $visitor = $this->createVisitor($value);

        foreach ($this->resolveGroups($groups) as $group) {
            $visitor->validate($value, $group, '', $traverse, $deep);
        }

        $validatorFactory = $visitor->getValidatorFactory();

        $refClass = new \ReflectionClass(get_class($validatorFactory));
        $property = $refClass->getProperty('validators');
        $property->setAccessible(true);

        $validators = $property->getValue($validatorFactory);
        $validators = array_filter($validators, function($validator) {
            if (!is_object($validator)) {
                return false;
            }

            return 0 === strpos(get_class($validator), 'Symfony\\Component\\Validator\\Constraints\\');
        });

        $rules = array();
        foreach ($validators as $validator) {
            /** @var $validator ConstraintValidator */
            $refClass = new \ReflectionClass(get_class($validator));
            $property = $refClass->getProperty('context');
            $property->setAccessible(true);

            /** @var $context ExecutionContext */
            $context = $property->getValue($validator);

            /** @var $metadata PropertyMetadata */
            $metadata = $context->getMetadata();

            foreach ($metadata->getConstraints() as $constraint) {
                /** @var $constraint Constraint */
                if ($constraint instanceof Length) {
                    $rules['length'] = array(
                        'property' => $metadata->getPropertyName(),
                        'maxMessage' => $constraint->maxMessage,
                        'minMessage' => $constraint->minMessage,
                        'exactMessage' => $constraint->exactMessage,
                        'max' => $constraint->max,
                        'min' => $constraint->min,
                    );
                }
            }
        }

        return $rules;
    }

    /**
     * @param mixed $root
     *
     * @return ValidationVisitor
     */
    private function createVisitor($root)
    {
        return new ValidationVisitor(
            $root,
            $this->metadataFactory,
            $this->validatorFactory,
            $this->translator,
            $this->translationDomain,
            $this->objectInitializers
        );
    }

    /**
     * @param null|string|string[] $groups
     *
     * @return string[]
     */
    private function resolveGroups($groups)
    {
        return $groups ? (array) $groups : array(Constraint::DEFAULT_GROUP);
    }
}