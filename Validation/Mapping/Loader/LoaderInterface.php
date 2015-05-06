<?php

namespace ITE\FormBundle\Validation\Mapping\Loader;

use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Loads validation metadata into {@link ClassMetadata} instances.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface LoaderInterface
{
    /**
     * @param ClassMetadata $metadata
     * @return bool
     */
    public function loadClassMetadata(ClassMetadata $metadata);
}
