<?php

namespace ITE\FormBundle\Validation\Mapping\Loader;

use ITE\FormBundle\Validation\ClientConstraint;
use Symfony\Component\Validator\Exception\MappingException;

/**
 * Base loader for validation metadata.
 *
 * This loader supports the loading of constraints from Symfony's default
 * namespace (see {@link DEFAULT_NAMESPACE}) using the short class names of
 * those constraints. Constraints can also be loaded using their fully
 * qualified class names. At last, namespace aliases can be defined to load
 * constraints with the syntax "alias:ShortName".
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractLoader implements LoaderInterface
{
    /**
     * The namespace to load constraints from by default.
     */
    const DEFAULT_NAMESPACE = '\\ITE\\FormBundle\\Validation\\Constraints\\';

    /**
     * @var array
     */
    protected $namespaces = [];

    /**
     * Adds a namespace alias.
     *
     * The namespace alias can be used to reference constraints from specific
     * namespaces in {@link newConstraint()}:
     *
     *     $this->addNamespaceAlias('mynamespace', '\\Acme\\Package\\Constraints\\');
     *
     *     $constraint = $this->newConstraint('mynamespace:NotNull');
     *
     * @param string $alias     The alias
     * @param string $namespace The PHP namespace
     */
    protected function addNamespaceAlias($alias, $namespace)
    {
        $this->namespaces[$alias] = $namespace;
    }

    /**
     * Creates a new constraint instance for the given constraint name.
     *
     * @param string $name    The constraint name. Either a constraint relative
     *                        to the default constraint namespace, or a fully
     *                        qualified class name. Alternatively, the constraint
     *                        may be preceded by a namespace alias and a colon.
     *                        The namespace alias must have been defined using
     *                        {@link addNamespaceAlias()}.
     * @param mixed  $options The constraint options
     *
     * @return ClientConstraint
     *
     * @throws MappingException If the namespace prefix is undefined
     */
    protected function newConstraint($name, $options = null)
    {
        if (strpos($name, '\\') !== false && class_exists($name)) {
            $className = (string) $name;
        } elseif (strpos($name, ':') !== false) {
            list($prefix, $className) = explode(':', $name, 2);

            if (!isset($this->namespaces[$prefix])) {
                throw new MappingException(sprintf('Undefined namespace prefix "%s"', $prefix));
            }

            $className = $this->namespaces[$prefix].$className;
        } else {
            $className = self::DEFAULT_NAMESPACE.$name;
        }

        return new $className($options);
    }
}
