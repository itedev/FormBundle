<?php

namespace ITE\FormBundle\Form\DataTransformer;

use ITE\FormBundle\Form\Data\Range;
use ITE\FormBundle\Form\Data\RangeInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class RangeToStringTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class RangeToStringTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var
     */
    protected $separator;

    /**
     * @var DataTransformerInterface
     */
    protected $transformer;

    /**
     * @param string $class
     * @param $separator
     * @param DataTransformerInterface $transformer
     */
    public function __construct($class, $separator, DataTransformerInterface $transformer = null)
    {
        $this->class = $class;
        $this->separator = $separator;
        $this->transformer = $transformer;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return;
        }

        if (!($value instanceof RangeInterface)) {
            throw new TransformationFailedException('Expected a \ITE\FormBundle\Form\Data\RangeInterface.');
        }

        $from = $value->getFrom();
        $to = $value->getTo();
        if (null !== $this->transformer) {
            $from = $this->transformer->transform($from);
            $to = $this->transformer->transform($to);
        }

        return implode($this->separator, [
            $from,
            $to,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return;
        }
        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }
        if ('' === $value) {
            return;
        }
        if (false === strpos($value, $this->separator)) {
            throw new TransformationFailedException(sprintf('Separator "%s" is not found', $this->separator));
        }

        list($from, $to) = explode($this->separator, $value, 2);
        if ('' === trim($from)) {
            $from = null;
        }
        if ('' === trim($to)) {
            $to = null;
        }

        if (null === $from && null === $to) {
            return;
        }

        if (null !== $this->transformer) {
            $from = $this->transformer->reverseTransform($from);
            $to = $this->transformer->reverseTransform($to);
        }

        $class = $this->class;
        /** @var RangeInterface $range */
        $range = new $class();
        $range->setFrom($from);
        $range->setTo($to);

        return $range;
    }
}
