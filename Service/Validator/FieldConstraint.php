<?php

namespace ITE\FormBundle\Service\Validator;

/**
 * Class FieldConstraint
 * @package ITE\FormBundle\Service\Validator
 */
class FieldConstraint
{
    /**
     * @var ConstraintMetadata $constraintMetadata
     */
    protected $constraintMetadata;

    /**
     * @var string $propertyPath
     */
    protected $propertyPath;

    /**
     * @param ConstraintMetadata $constraintMetadata
     * @param $propertyPath
     */
    public function __construct(ConstraintMetadata $constraintMetadata, $propertyPath)
    {
        $this->constraintMetadata = $constraintMetadata;
        $this->propertyPath = $propertyPath;
    }

    /**
     * Get constraintMetadata
     *
     * @return ConstraintMetadata
     */
    public function getConstraintMetadata()
    {
        return $this->constraintMetadata;
    }

    /**
     * Get propertyPath
     *
     * @return string
     */
    public function getPropertyPath()
    {
        return $this->propertyPath;
    }


} 