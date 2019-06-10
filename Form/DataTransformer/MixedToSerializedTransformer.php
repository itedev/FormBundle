<?php

namespace ITE\FormBundle\Form\DataTransformer;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class MixedToSerializedTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MixedToSerializedTransformer implements DataTransformerInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $dataClass;

    /**
     * @var string
     */
    private $format;

    /**
     * @var array
     */
    private $groups;

    /**
     * MixedToSerializedTransformer constructor.
     *
     * @param SerializerInterface $serializer
     * @param string $dataClass
     * @param string $format
     * @param array $groups
     */
    public function __construct(
        SerializerInterface $serializer,
        $dataClass,
        $format,
        array $groups
    ) {
        $this->serializer = $serializer;
        $this->dataClass = $dataClass;
        $this->format = $format;
        $this->groups = $groups;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return;
        }

        return $this->serializer->serialize($value, $this->format, SerializationContext::create()->setGroups($this->groups));
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        return $this->serializer->deserialize($value, $this->dataClass, $this->format);
    }
}
