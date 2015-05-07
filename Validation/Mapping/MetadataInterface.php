<?php

namespace ITE\FormBundle\Validation\Mapping;

use ITE\FormBundle\Validation\Constraint;

/**
 * A container for validation metadata.
 *
 * Most importantly, the metadata stores the constraints against which an object
 * and its properties should be validated.
 *
 * Additionally, the metadata stores whether objects should be validated
 * against their class' metadata and whether traversable objects should be
 * traversed or not.
 *
 * @since  2.5
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface MetadataInterface
{
    /**
     * Returns all constraints of this element.
     *
     * @return Constraint[] A list of Constraint instances
     */
    public function getConstraints();

    /**
     * Returns all constraints for a given validation group.
     *
     * @param string $group The validation group
     *
     * @return Constraint[] A list of constraint instances
     */
    public function findConstraints($group);
}
