<?php

namespace ITE\FormBundle\Validation;

use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * Class Constraint
 *
 * @see Symfony\Component\Validator\Constraint
 *
 * @property array $groups The groups that the constraint belongs to
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class Constraint
{
    /**
     * The name of the group given to all constraints with no explicit group.
     *
     * @var string
     */
    const DEFAULT_GROUP = 'Default';

    /**
     * Marks a constraint that can be put onto classes.
     *
     * @var string
     */
    const CLASS_CONSTRAINT = 'class';

    /**
     * Marks a constraint that can be put onto properties.
     *
     * @var string
     */
    const PROPERTY_CONSTRAINT = 'property';

    /**
     * @var array $attributes
     */
    protected $attributes = [];

    /**
     * @param null $options
     */
    public function __construct($options = null)
    {
        $invalidOptions = [];
        $missingOptions = array_flip((array) $this->getRequiredOptions());
        $knownOptions = get_object_vars($this);

        // The "groups" option is added to the object lazily
        $knownOptions['groups'] = true;

        if (is_array($options) && count($options) >= 1 && isset($options['value']) && !property_exists($this, 'value')) {
            $options[$this->getDefaultOption()] = $options['value'];
            unset($options['value']);
        }

        if (is_array($options) && count($options) > 0 && is_string(key($options))) {
            foreach ($options as $option => $value) {
                if (array_key_exists($option, $knownOptions)) {
                    $this->$option = $value;
                    unset($missingOptions[$option]);
                } else {
                    $invalidOptions[] = $option;
                }
            }
        } elseif (null !== $options && !(is_array($options) && count($options) === 0)) {
            $option = $this->getDefaultOption();

            if (null === $option) {
                throw new ConstraintDefinitionException(
                    sprintf('No default option is configured for constraint %s', get_class($this))
                );
            }

            if (array_key_exists($option, $knownOptions)) {
                $this->$option = $options;
                unset($missingOptions[$option]);
            } else {
                $invalidOptions[] = $option;
            }
        }

        if (count($invalidOptions) > 0) {
            throw new InvalidOptionsException(
                sprintf('The options "%s" do not exist in constraint %s', implode('", "', $invalidOptions), get_class($this)),
                $invalidOptions
            );
        }

        if (count($missingOptions) > 0) {
            throw new MissingOptionsException(
                sprintf('The options "%s" must be set for constraint %s', implode('", "', array_keys($missingOptions)), get_class($this)),
                array_keys($missingOptions)
            );
        }
    }

    /**
     * Sets the value of a lazily initialized option.
     *
     * Corresponding properties are added to the object on first access. Hence
     * this method will be called at most once per constraint instance and
     * option name.
     *
     * @param string $option The option name
     * @param mixed  $value  The value to set
     *
     * @throws InvalidOptionsException If an invalid option name is given
     */
    public function __set($option, $value)
    {
        if ('groups' === $option) {
            $this->groups = (array) $value;

            return;
        }

        throw new InvalidOptionsException(sprintf('The option "%s" does not exist in constraint %s', $option, get_class($this)), array($option));
    }

    /**
     * Returns the value of a lazily initialized option.
     *
     * Corresponding properties are added to the object on first access. Hence
     * this method will be called at most once per constraint instance and
     * option name.
     *
     * @param string $option The option name
     *
     * @return mixed The value of the option
     *
     * @throws InvalidOptionsException If an invalid option name is given
     *
     * @internal This method should not be used or overwritten in userland code.
     *
     * @since 2.6
     */
    public function __get($option)
    {
        if ('groups' === $option) {
            $this->groups = array(self::DEFAULT_GROUP);

            return $this->groups;
        }

        throw new InvalidOptionsException(sprintf('The option "%s" does not exist in constraint %s', $option, get_class($this)), array($option));
    }

    /**
     * Adds the given group if this constraint is in the Default group.
     *
     * @param string $group
     *
     * @api
     */
    public function addImplicitGroupName($group)
    {
        if (in_array(Constraint::DEFAULT_GROUP, $this->groups) && !in_array($group, $this->groups)) {
            $this->groups[] = $group;
        }
    }

    /**
     * Returns the name of the default option.
     *
     * Override this method to define a default option.
     *
     * @return string
     *
     * @see __construct()
     *
     * @api
     */
    public function getDefaultOption()
    {
    }

    /**
     * Returns the name of the required options.
     *
     * Override this method if you want to define required options.
     *
     * @return array
     *
     * @see __construct()
     *
     * @api
     */
    public function getRequiredOptions()
    {
        return array();
    }

    /**
     * Returns whether the constraint can be put onto classes, properties or
     * both.
     *
     * This method should return one or more of the constants
     * Constraint::CLASS_CONSTRAINT and Constraint::PROPERTY_CONSTRAINT.
     *
     * @return string|array One or more constant values
     *
     * @api
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     * @return Constraint
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param null $defaultValue
     * @return null
     */
    public function getAttribute($name, $defaultValue = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $defaultValue;
    }
}