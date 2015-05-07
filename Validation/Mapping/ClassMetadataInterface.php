<?php

namespace ITE\FormBundle\Validation\Mapping;

/**
 * Stores all metadata needed for validating objects of specific class.
 *
 * Most importantly, the metadata stores the constraints against which an object
 * and its properties should be validated.
 *
 * Additionally, the metadata stores whether the "Default" group is overridden
 * by a group sequence for that class and whether instances of that class
 * should be traversed or not.
 *
 * @since  2.5
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 *
 * @see MetadataInterface
 */
interface ClassMetadataInterface extends MetadataInterface
{
    /**
     * Returns the names of all constrained properties.
     *
     * @return string[] A list of property names
     */
    public function getConstrainedProperties();

    /**
     * Check if there's any metadata attached to the given named property.
     *
     * @param string $property The property name.
     *
     * @return bool
     */
    public function hasPropertyMetadata($property);

    /**
     * Returns all metadata instances for the given named property.
     *
     * If your implementation does not support properties, simply throw an
     * exception in this method (for example a <tt>BadMethodCallException</tt>).
     *
     * @param string $property The property name.
     *
     * @return PropertyMetadataInterface[] A list of metadata instances. Empty if
     *                                     no metadata exists for the property.
     */
    public function getPropertyMetadata($property);
}
