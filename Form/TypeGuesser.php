<?php

namespace ITE\FormBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * Class TypeGuesser
 * @package ITE\FormBundle\Form
 */
class TypeGuesser implements FormTypeGuesserInterface
{
    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function guessType($className, $property)
    {
        $classMetadata = $this->em->getClassMetadata($className);

        if (!isset($classMetadata->propertyMetadata[$property])) {
            return null;
        }

        return new TypeGuess(
            $classMetadata->propertyMetadata[$property]->type,
            $classMetadata->propertyMetadata[$property]->options,
            Guess::HIGH_CONFIDENCE + 1
        );
    }

    /**
     * {@inheritdoc}
     */
    public function guessRequired($class, $property)
    {
        return null;
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