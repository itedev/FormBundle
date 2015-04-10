<?php

namespace ITE\FormBundle\Service\Validation;

/**
 * Interface ConstraintConverterInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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