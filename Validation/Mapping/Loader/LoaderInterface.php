<?php

namespace ITE\FormBundle\Validation\Mapping\Loader;

use ITE\FormBundle\Validation\Mapping\ClassMetadata;

/**
 * Loads validation metadata into {@link ClassMetadata} instances.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface LoaderInterface
{
    /**
     * @param ClassMetadata $metadata
     * @return bool
     */
    public function loadClassMetadata(ClassMetadata $metadata);
}
