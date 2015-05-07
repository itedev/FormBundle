<?php

namespace ITE\FormBundle\Validation\Mapping;

/**
 * Stores all metadata needed for validating the value of a class property.
 *
 * Most importantly, the metadata stores the constraints against which the
 * property's value should be validated.
 *
 * Additionally, the metadata stores whether objects stored in the property
 * should be validated against their class' metadata and whether traversable
 * objects should be traversed or not.
 *
 * @since  2.5
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 *
 * @see MetadataInterface
 */
interface PropertyMetadataInterface extends MetadataInterface
{
    /**
     * Returns the name of the backing PHP class.
     *
     * @return string The name of the backing class.
     */
    public function getClassName();

    /**
     * Returns the name of the property.
     *
     * @return string The property name.
     */
    public function getPropertyName();

    /**
     * Extracts the value of the property from the given container.
     *
     * @param mixed $containingValue The container to extract the property value from.
     *
     * @return mixed The value of the property.
     */
    public function getPropertyValue($containingValue);
}
