<?php

namespace ITE\FormBundle\Validation;

use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\InvalidOptionsException;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * Class Constraint
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class Constraint
{
    const DEFAULT_GROUP = 'Default';

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
     * @param string $option
     * @param mixed $value
     */
    public function __set($option, $value)
    {
        if ('groups' === $option) {
            $this->groups = (array) $value;

            return;
        }

        throw new InvalidOptionsException(sprintf('The option "%s" does not exist in constraint %s', $option, get_class($this)), [$option]);
    }

    /**
     * @param string $option
     * @return array
     */
    public function __get($option)
    {
        if ('groups' === $option) {
            $this->groups = [self::DEFAULT_GROUP];

            return $this->groups;
        }

        throw new InvalidOptionsException(sprintf('The option "%s" does not exist in constraint %s', $option, get_class($this)), [$option]);
    }

    /**
     * @return string
     */
    public function getDefaultOption()
    {
    }

    /**
     * @return array
     */
    public function getRequiredOptions()
    {
        return [];
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