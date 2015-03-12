<?php

namespace ITE\FormBundle\Form;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use ITE\FormBundle\Annotation\Type;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * Class TypeGuesser
 * @package ITE\FormBundle\Form
 */
class TypeGuesser implements FormTypeGuesserInterface
{
    const TYPE_ANNOTATION = '\ITE\FormBundle\Annotation\Type';

    /**
     * @var Reader $reader
     */
    protected $reader;

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @param Reader $reader
     * @param EntityManager $em
     */
    public function __construct(Reader $reader, EntityManager $em)
    {
        $this->reader = $reader;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($className, $property)
    {
        /** @var $classMetadata ClassMetadataInfo */
        $classMetadata = $this->em->getClassMetadata($className);
        $reflProperty = $classMetadata->getReflectionProperty($property);
        /** @var $typeAnnotation Type */
        $typeAnnotation = $this->reader->getPropertyAnnotation($reflProperty, self::TYPE_ANNOTATION);
        if (!$typeAnnotation) {
            return null;
        }

        return new TypeGuess(
            $typeAnnotation->getType(),
            $typeAnnotation->getOptions(),
            Guess::VERY_HIGH_CONFIDENCE
        );
    }

    /**
     * {@inheritdoc}
     */
    public function guessRequired($class, $property)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function guessMaxLength($class, $property)
    {
    }

    public function guessPattern($class, $property)
    {
    }
}