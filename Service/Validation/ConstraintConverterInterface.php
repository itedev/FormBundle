<?php

namespace ITE\FormBundle\Service\Validation;

/**
 * Interface ConstraintConverterInterface
 * @package ITE\FormBundle\Service\Validation
 */
interface ConstraintConverterInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return array
     */
    public function getOptions();
} 