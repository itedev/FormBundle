<?php

namespace ITE\FormBundle\Validation\Mapping\Loader;

use Doctrine\Common\Annotations\Reader;
use ITE\FormBundle\Validation\ClientConstraint;
use ITE\FormBundle\Validation\Mapping\ClassMetadata;

/**
 * Loads validation metadata using a Doctrine annotation {@link Reader}.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
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

        foreach ($this->reader->getClassAnnotations($reflClass) as $constraint) {
            if ($constraint instanceof ClientConstraint) {
                $metadata->addConstraint($constraint);
            }

            $success = true;
        }

        foreach ($reflClass->getProperties() as $property) {
            if ($property->getDeclaringClass()->name == $className) {
                foreach ($this->reader->getPropertyAnnotations($property) as $constraint) {
                    if ($constraint instanceof ClientConstraint) {
                        $metadata->addPropertyConstraint($property->name, $constraint);
                    }

                    $success = true;
                }
            }
        }

        return $success;
    }
}
