<?php

namespace ITE\FormBundle\Service;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\MetadataFactoryInterface;
use Symfony\Component\Validator\ValidationVisitor;

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
    public function getValidators($value, $groups = null, $traverse = false, $deep = false)
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

        return $validators;
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