<?php

namespace ITE\FormBundle\Service\Validator;

use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\NoSuchMetadataException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\MetadataFactoryInterface;
use Symfony\Component\Validator\MetadataInterface;
use Symfony\Component\Validator\ObjectInitializerInterface;
use Symfony\Component\Validator\ValidationVisitorInterface;

/**
 * Class ValidationVisitor
 * @package ITE\FormBundle\Service\Validator
 */
class ValidationVisitor implements ValidationVisitorInterface, GlobalExecutionContextInterface
{
    /**
     * @var mixed
     */
    protected $root;

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
     * @var ConstraintViolationList
     */
    protected $violations;

    /**
     * @var array
     */
    protected $validatedObjects = array();

    /**
     * @var ConstraintMetadataFactory $constraintMetadataFactory
     */
    protected $constraintMetadataFactory;

    /**
     * @var array $constraints
     */
    protected $constraints = array();

    /**
     * @param $root
     * @param MetadataFactoryInterface $metadataFactory
     * @param ConstraintValidatorFactoryInterface $validatorFactory
     * @param TranslatorInterface $translator
     * @param null $translationDomain
     * @param array $objectInitializers
     * @throws UnexpectedTypeException
     */
    public function __construct($root, MetadataFactoryInterface $metadataFactory, ConstraintValidatorFactoryInterface $validatorFactory, TranslatorInterface $translator, $translationDomain = null, array $objectInitializers = array())
    {
        foreach ($objectInitializers as $initializer) {
            if (!$initializer instanceof ObjectInitializerInterface) {
                throw new UnexpectedTypeException($initializer, 'Symfony\Component\Validator\ObjectInitializerInterface');
            }
        }

        $this->root = $root;
        $this->metadataFactory = $metadataFactory;
        $this->validatorFactory = $validatorFactory;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
        $this->objectInitializers = $objectInitializers;
        $this->violations = new ConstraintViolationList();
        $this->constraintMetadataFactory = new ConstraintMetadataFactory($this->translator, $this->translationDomain);
    }

    /**
     * {@inheritdoc}
     */
    public function visit(MetadataInterface $metadata, $value, $group, $propertyPath)
    {
        $context = new ExecutionContext(
            $this,
            $this->translator,
            $this->translationDomain,
            $metadata,
            $value,
            $group,
            $propertyPath
        );

        $context->validateValue($value, $metadata->findConstraints($group));
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, $group, $propertyPath, $traverse = false, $deep = false)
    {
        if (null === $value) {
            return;
        }

        if (is_object($value)) {
            $hash = spl_object_hash($value);

            // Exit, if the object is already validated for the current group
            if (isset($this->validatedObjects[$hash][$group])) {
                return;
            }

            // Remember validating this object before starting and possibly
            // traversing the object graph
            $this->validatedObjects[$hash][$group] = true;

            foreach ($this->objectInitializers as $initializer) {
                if (!$initializer instanceof ObjectInitializerInterface) {
                    throw new \LogicException('Validator initializers must implement ObjectInitializerInterface.');
                }
                $initializer->initialize($value);
            }
        }

        // Validate arrays recursively by default, otherwise every driver needs
        // to implement special handling for arrays.
        // https://github.com/symfony/symfony/issues/6246
        if (is_array($value) || ($traverse && $value instanceof \Traversable)) {
            foreach ($value as $key => $element) {
                // Ignore any scalar values in the collection
                if (is_object($element) || is_array($element)) {
                    // Only repeat the traversal if $deep is set
                    $this->validate($element, $group, $propertyPath.'['.$key.']', $deep, $deep);
                }
            }

            try {
                $this->metadataFactory->getMetadataFor($value)->accept($this, $value, $group, $propertyPath);
            } catch (NoSuchMetadataException $e) {
                // Metadata doesn't necessarily have to exist for
                // traversable objects, because we know how to validate
                // them anyway. Optionally, additional metadata is supported.
            }
        } else {
            $this->metadataFactory->getMetadataFor($value)->accept($this, $value, $group, $propertyPath);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * {@inheritdoc}
     */
    public function getVisitor()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidatorFactory()
    {
        return $this->validatorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraintMetadataFactory()
    {
        return $this->constraintMetadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function addConstraint(FieldConstraint $constraint)
    {
        $this->constraints[] = $constraint;
    }
} 