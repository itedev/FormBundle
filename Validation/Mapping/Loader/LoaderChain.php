<?php

namespace ITE\FormBundle\Validation\Mapping\Loader;

use ITE\FormBundle\Validation\Mapping\ClassMetadata;
use Symfony\Component\Validator\Exception\MappingException;

/**
 * Loads validation metadata from multiple {@link LoaderInterface} instances.
 *
 * Pass the loaders when constructing the chain. Once
 * {@link loadClassMetadata()} is called, that method will be called on all
 * loaders in the chain.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class LoaderChain implements LoaderInterface
{
    /**
     * @var LoaderInterface[]
     */
    protected $loaders;

    /**
     * @param LoaderInterface[] $loaders The metadata loaders to use
     *
     * @throws MappingException If any of the loaders has an invalid type
     */
    public function __construct(array $loaders)
    {
        foreach ($loaders as $loader) {
            if (!$loader instanceof LoaderInterface) {
                throw new MappingException(sprintf('Class %s is expected to implement LoaderInterface', get_class($loader)));
            }
        }

        $this->loaders = $loaders;
    }

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadata $metadata)
    {
        $success = false;

        foreach ($this->loaders as $loader) {
            $success = $loader->loadClassMetadata($metadata) || $success;
        }

        return $success;
    }
}
