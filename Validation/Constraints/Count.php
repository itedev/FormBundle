<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * Class Count
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\Count
 * @see Symfony\Component\Validator\Constraints\CountValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Count extends ClientConstraint
{
    public $minMessage = 'This collection should contain {{ limit }} element or more.|This collection should contain {{ limit }} elements or more.';
    public $maxMessage = 'This collection should contain {{ limit }} element or less.|This collection should contain {{ limit }} elements or less.';
    public $exactMessage = 'This collection should contain exactly {{ limit }} element.|This collection should contain exactly {{ limit }} elements.';
    public $min;
    public $max;

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        if (null !== $options && !is_array($options)) {
            $options = [
                'min' => $options,
                'max' => $options,
            ];
        }

        parent::__construct($options);

        if (null === $this->min && null === $this->max) {
            throw new MissingOptionsException(sprintf('Either option "min" or "max" must be given for constraint %s', __CLASS__), ['min', 'max']);
        }
    }
}
