<?php

namespace ITE\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class BooleanToStringTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class BooleanToStringTransformer implements DataTransformerInterface
{
    /**
     * @var bool
     */
    protected $required;

    /**
     * @param bool $required
     */
    public function __construct($required)
    {
        $this->required = $required;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return $this->required ? 0 : null;
        }
        
        if (!is_bool($value)) {
            throw new TransformationFailedException('Expected a Boolean.');
        }

        return $value ? 1 : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return $this->required ? false : null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        return (bool) $value;
    }
}
