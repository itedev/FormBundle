<?php

namespace ITE\FormBundle\Validation\Mapping\Loader;

use Doctrine\Common\Annotations\Reader;
use ITE\FormBundle\Validation\Constraint;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Loads validation metadata using a Doctrine annotation {@link Reader}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AnnotationLoader implements LoaderInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadata $metadata)
    {
        $reflClass = $metadata->getReflectionClass();
        $className = $reflClass->name;
        $success = false;

//        foreach ($this->reader->getClassAnnotations($reflClass) as $constraint) {
//            if ($constraint instanceof Constraint) {
//                $metadata->addConstraint($constraint);
//            }
//
//            $success = true;
//        }

        foreach ($reflClass->getProperties() as $property) {
            if ($property->getDeclaringClass()->name == $className) {
                foreach ($this->reader->getPropertyAnnotations($property) as $constraint) {
                    if ($constraint instanceof Constraint) {
                        $metadata->addPropertyConstraint($property->name, $constraint);
                    }

                    $success = true;
                }
            }
        }

        return $success;
    }
}
