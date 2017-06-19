<?php

namespace ITE\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class LowerToUpperCaseTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class LowerToUpperCaseTransformer implements DataTransformerInterface
{
    /**
     * @var bool $inversed
     */
    private $inversed;

    /**
     * @param bool $inversed
     */
    public function __construct($inversed = false)
    {
        $this->inversed = $inversed;
    }

    /**
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $function = !$this->inversed ? 'strtoupper' : 'strtolower';

        return call_user_func_array($function, [$value]);
    }

    /**
     * @param string $value
     * @return array
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $function = !$this->inversed ? 'strtolower' : 'strtoupper';

        return call_user_func_array($function, [$value]);
    }
}
