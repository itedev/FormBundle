<?php

namespace ITE\FormBundle\Service\Validation;

use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\MetadataFactoryInterface;

/**
 * Class ConstraintExtractor
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ConstraintExtractor implements ConstraintExtractorInterface
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
    public function getConstraints($value)
    {
        $visitor = $this->createVisitor($value);

        $this->getConstraintsFromData($visitor, $value);
        $this->getConstraintsFromForm($visitor, $value);

        return $visitor->getConstraints();
    }

    /**
     * @param GlobalExecutionContextInterface $visitor
     * @param FormInterface $form
     */
    protected function getConstraintsFromData(GlobalExecutionContextInterface $visitor, FormInterface $form)
    {
        foreach ($this->resolveGroups(null) as $group) {
            $visitor->validate($form, $group, '', false, false);
        }
    }

    /**
     * @param GlobalExecutionContextInterface $visitor
     * @param FormInterface $form
     */
    protected function getConstraintsFromForm(GlobalExecutionContextInterface $visitor, FormInterface $form)
    {
        FormUtils::formWalkRecursive($form, function(FormInterface $child) use ($visitor) {
            if (null !== $constraintMetadata = $visitor->getConstraintMetadataFactory()->getMetadataForForm($child)) {
                $formConstraint = new FormConstraint($child, $constraintMetadata);
                $visitor->addConstraint($formConstraint);
            }
        });
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