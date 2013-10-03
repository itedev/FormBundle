<?php

namespace ITE\FormBundle\Exception;

use Symfony\Component\Form\Exception\InvalidConfigurationException;

/**
 * Ordered configuration exception.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class OrderedConfigurationException extends InvalidConfigurationException
{
    /**
     * Creates a "CIRCULAR DIFFERED" exception.
     *
     * @param array  $stack    The circular stack.
     * @param string $position The position (before|after).
     *
     * @return OrderedConfigurationException The "CIRCULAR DIFFERED" exception.
     */
    public static function createCircularDiffered(array $stack, $position)
    {
        $stack[] = $stack[0];

        return new self(sprintf(
            'The form ordering cannot be resolved due to conflict in %s positions (%s).',
            $position,
            implode(' => ', self::decorateValues($stack))
        ));
    }

    /**
     * Creates an "INVALID DIFFERED" exception.
     *
     * @param string $name     The form name.
     * @param string $position The position (before|after).
     * @param string $differed The differed form name.
     *
     * @return OrderedConfigurationException The "INVALID DIFFERED" exception.
     */
    public static function createInvalidDiffered($name, $position, $differed)
    {
        $decoratedDiffered = self::decorateValue($differed);

        return new self(sprintf(
            'The %s form is configured to be placed just %s the form %s but the form %s does not exist.',
            self::decorateValue($name),
            $position,
            $decoratedDiffered,
            $decoratedDiffered
        ));
    }

    /**
     * Creates an "INVALID STRING POSITION" exception.
     *
     * @param string $name     The form name.
     * @param string $position The invalid string position.
     *
     *
     * @return OrderedConfigurationException The "INVALID STRING POSITION" exception.
     */
    public static function createInvalidStringPosition($name, $position)
    {
        return new self(sprintf(
            'The %s form uses position as string which can only be "first" or "last" (current: %s).',
            self::decorateValue($name),
            self::decorateValue($position)
        ));
    }

    /**
     * Creates an "INVALID ARRAY CONFIGURATION" exception.
     *
     * @param string $name     The form name.
     * @param array  $position The invalid array position.
     *
     * @return OrderedConfigurationException The "INVALID ARRAY CONFIGURATION" exception.
     */
    public static function createInvalidArrayPosition($name, array $position)
    {
        return new self(sprintf(
            'The %s form uses position as array or you must define the "before" or "after" option (current: %s).',
            self::decorateValue($name),
            implode(', ', self::decorateValues(array_keys($position)))
        ));
    }

    /**
     * Creates a "SYMETRIC DIFFERED" exception.
     *
     * @param string $name     The form name.
     * @param string $symetric The symectric form name.
     *
     * @return OrderedConfigurationException The "SYMETRIC DIFFERED" exception.
     */
    public static function createSymetricDiffered($name, $symetric)
    {
        return new self(sprintf(
            'The form ordering does not support symetrical before/after option (%s <=> %s).',
            self::decorateValue($name),
            self::decorateValue($symetric)
        ));
    }

    /**
     * Decorates values with the decorator.
     *
     * @param array  $values    The values.
     * @param string $decorator The decorator.
     *
     * @return array The decorated values.
     */
    protected static function decorateValues(array $values, $decorator = '"')
    {
        $result = array();

        foreach ($values as $key => $value) {
            $result[$key] = self::decorateValue($value, $decorator);
        }

        return $result;
    }

    /**
     * Decorates a value with the decorator.
     *
     * @param string $value     The value.
     * @param string $decorator The decorator.
     *
     * @return string The decorated value.
     */
    protected static function decorateValue($value, $decorator = '"')
    {
        return $decorator.$value.$decorator;
    }
}