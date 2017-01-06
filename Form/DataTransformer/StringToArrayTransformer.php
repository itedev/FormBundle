<?php

namespace ITE\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class StringToArrayTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class StringToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var string $separator
     */
    private $separator;

    /**
     * @param string $separator
     */
    public function __construct($separator = ',')
    {
        $this->separator = $separator;
    }

    /**
     * @param mixed $values
     * @return string
     */
    public function transform($values)
    {
        if (null === $values) {
            return;
        }

        if (!is_array($values)) {
            throw new TransformationFailedException('Expected an array.');
        }

        return implode($this->separator, $values);
    }

    /**
     * @param mixed $values
     * @return array
     */
    public function reverseTransform($values)
    {
        if (null === $values) {
            return [];
        }

        if (!is_string($values)) {
            throw new TransformationFailedException('Expected a string.');
        }

        return preg_split(sprintf('~%s~', preg_quote($this->separator, '~')), $values, -1, PREG_SPLIT_NO_EMPTY);
    }
}
