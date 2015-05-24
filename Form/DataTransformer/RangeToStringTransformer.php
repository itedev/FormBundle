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
     * @param string $class
     * @param $separator
     */
    public function __construct($class, $separator)
    {
        $this->class = $class;
        $this->separator = $separator;
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

        return implode($this->separator, [
            $value->getFrom(),
            $value->getTo(),
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

        list($from, $to) = explode($this->separator, $value);

        $class = $this->class;
        /** @var RangeInterface $range */
        $range = new $class();
        $range->setFrom($from);
        $range->setTo($to);

        return $range;
    }
}