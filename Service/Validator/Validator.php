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
class Validator implements ValidatorInterface
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
     * @param $form
     * @return array
     */
    public function getConstraints($form)
    {
        $visitor = $this->createVisitor($form);

        foreach ($this->resolveGroups(null) as $group) {
            $visitor->validate($form, $group, '', false, false);
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