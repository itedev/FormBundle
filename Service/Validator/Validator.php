<?php

namespace ITE\FormBundle\Service\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\MetadataFactoryInterface;

/**
 * Class Validator
 * @package ITE\FormBundle\Service\Validator
 */
class Validator
{
    /**
     * @var MetadataFactoryInterface
     */
    protected $metadataFactory;

    /**
     * @var ConstraintValidatorFactoryInterface
     */
    protected $validatorFactory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var null|string
     */
    protected $translationDomain;

    /**
     * @var array
     */
    protected $objectInitializers;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     * @param ConstraintValidatorFactoryInterface $validatorFactory
     * @param TranslatorInterface $translator
     * @param string $translationDomain
     * @param array $objectInitializers
     */
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
     * {@inheritdoc}
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadataFor($value)
    {
        return $this->metadataFactory->getMetadataFor($value);
    }

    /**
     * {@inheritDoc}
     */
    public function validate($value, $groups = null, $traverse = false, $deep = false)
    {
        $visitor = $this->createVisitor($value);

        foreach ($this->resolveGroups($groups) as $group) {
            $visitor->validate($value, $group, '', $traverse, $deep);
        }

        return $visitor->getConstraints();
    }

    /**
     * @param mixed $root
     *
     * @return ValidationVisitor
     */
    protected function createVisitor($root)
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
    protected function resolveGroups($groups)
    {
        return $groups ? (array) $groups : array(Constraint::DEFAULT_GROUP);
    }
} 