<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * Class Length
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\Length
 * @see Symfony\Component\Validator\Constraints\LengthValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Length extends ClientConstraint
{
    public $maxMessage = 'This value is too long. It should have {{ limit }} character or less.|This value is too long. It should have {{ limit }} characters or less.';
    public $minMessage = 'This value is too short. It should have {{ limit }} character or more.|This value is too short. It should have {{ limit }} characters or more.';
    public $exactMessage = 'This value should have exactly {{ limit }} character.|This value should have exactly {{ limit }} characters.';
    public $charsetMessage = 'This value does not match the expected {{ charset }} charset.';
    public $max;
    public $min;
    public $charset = 'UTF-8';

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