<?php

namespace ITE\FormBundle\Validation\Mapping\Loader;

use ITE\FormBundle\Validation\Mapping\ClassMetadata;
use Symfony\Component\Validator\Exception\MappingException;

/**
 * Loads validation metadata by calling a static method on the loaded class.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class StaticMethodLoader implements LoaderInterface
{
    /**
     * The name of the method to call.
     *
     * @var string
     */
    protected $methodName;

    /**
     * Creates a new loader.
     *
     * @param string $methodName The name of the static method to call
     */
    public function __construct($methodName = 'loadValidatorMetadata')
    {
        $this->methodName = $methodName;
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadata $metadata)
    {
        /** @var \ReflectionClass $reflClass */
        $reflClass = $metadata->getReflectionClass();

        if (!$reflClass->isInterface() && $reflClass->hasMethod($this->methodName)) {
            $reflMethod = $reflClass->getMethod($this->methodName);

            if ($reflMethod->isAbstract()) {
                return false;
            }

            if (!$reflMethod->isStatic()) {
                throw new MappingException(sprintf('The method %s::%s should be static', $reflClass->name, $this->methodName));
            }

            if ($reflMethod->getDeclaringClass()->name != $reflClass->name) {
                return false;
            }

            $reflMethod->invoke(null, $metadata);

            return true;
        }

        return false;
    }
}
