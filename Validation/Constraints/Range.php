<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * Class Range
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\Range
 * @see Symfony\Component\Validator\Constraints\RangeValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Range extends ClientConstraint
{
    public $minMessage = 'This value should be {{ limit }} or more.';
    public $maxMessage = 'This value should be {{ limit }} or less.';
    public $invalidMessage = 'This value should be a valid number.';
    public $message = 'This value should be between {{ min }} and {{ max }}.';
    public $min;
    public $max;

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if (null === $this->min && null === $this->max) {
            throw new MissingOptionsException(sprintf('Either option "min" or "max" must be given for constraint %s', __CLASS__), ['min', 'max']);
        }
    }
}