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
     * @var array|FormTypeGuesserInterface[]
     */
    protected $guessers = [];

    /**
     * @param Reader $reader
     * @param EntityManager $em
     * @param array|FormTypeGuesserInterface[] $guessers
     */
    public function __construct(Reader $reader, EntityManager $em, $guessers = [])
    {
        $this->reader = $reader;
        $this->em = $em;
        $this->guessers = $guessers;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($className, $property)
    {
        /** @var $classMetadata ClassMetadataInfo */
        $classMetadata = $this->em->getClassMetadata($className);
        $refProp = $classMetadata->getReflectionProperty($property);
        /** @var $typeAnnotation Type */
        $typeAnnotation = $this->reader->getPropertyAnnotation($refProp, self::TYPE_ANNOTATION);
        if (!$typeAnnotation) {
            return null;
        }

        $options = [];
        foreach ($this->guessers as $guesser) {
            $guess = $guesser->guessType($className, $property);
            if ($guess instanceof TypeGuess) {
                $options = array_merge($options, $guess->getOptions());
            }
        }

        return new TypeGuess(
            $typeAnnotation->getType(),
            array_merge($options, $typeAnnotation->getOptions()),
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

    /**
     * {@inheritdoc}
     */
    public function guessPattern($class, $property)
    {
    }
}