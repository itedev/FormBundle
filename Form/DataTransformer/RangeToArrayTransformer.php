<?php

namespace ITE\FormBundle\Form\DataTransformer;

use ITE\FormBundle\Form\Data\Range;
use ITE\FormBundle\Form\Data\RangeInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class RangeToArrayTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class RangeToArrayTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $fromName;

    /**
     * @var string
     */
    protected $toName;

    /**
     * @param string $class
     * @param string $fromName
     * @param string $toName
     */
    public function __construct($class, $fromName, $toName)
    {
        $this->class = $class;
        $this->fromName = $fromName;
        $this->toName = $toName;
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

        return [
            $this->fromName => $value->getFrom(),
            $this->toName => $value->getTo(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $from = isset($value[$this->fromName]) ? $value[$this->fromName] : null;
        $to = isset($value[$this->toName]) ? $value[$this->toName] : null;

        if (null === $from && null === $to) {
            return;
        }

        $class = $this->class;
        /** @var RangeInterface $range */
        $range = new $class();
        $range->setFrom($from);
        $range->setTo($to);

        return $range;
    }
}
