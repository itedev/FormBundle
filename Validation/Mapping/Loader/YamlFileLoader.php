<?php

namespace ITE\FormBundle\Validation\Mapping\Loader;

use ITE\FormBundle\Validation\Mapping\ClassMetadata;
use Symfony\Component\Yaml\Parser as YamlParser;

/**
 * Loads validation metadata from a YAML file.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class YamlFileLoader extends FileLoader
{
    /**
     * An array of YAML class descriptions.
     *
     * @var array
     */
    protected $classes = null;

    /**
     * Caches the used YAML parser.
     *
     * @var YamlParser
     */
    private $yamlParser;

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadata $metadata)
    {
        if (null === $this->classes) {
            if (null === $this->yamlParser) {
                $this->yamlParser = new YamlParser();
            }

            // This method may throw an exception. Do not modify the class'
            // state before it completes
            if (false === ($classes = $this->parseFile($this->file))) {
                return false;
            }

            $this->classes = $classes;

            if (isset($this->classes['namespaces'])) {
                foreach ($this->classes['namespaces'] as $alias => $namespace) {
                    $this->addNamespaceAlias($alias, $namespace);
                }

                unset($this->classes['namespaces']);
            }
        }

        if (isset($this->classes[$metadata->getClassName()])) {
            $classDescription = $this->classes[$metadata->getClassName()];

            $this->loadClassMetadataFromYaml($metadata, $classDescription);

            return true;
        }

        return false;
    }

    /**
     * Parses a collection of YAML nodes.
     *
     * @param array $nodes The YAML nodes
     *
     * @return array An array of values or Constraint instances
     */
    protected function parseNodes(array $nodes)
    {
        $values = [];

        foreach ($nodes as $name => $childNodes) {
            if (is_numeric($name) && is_array($childNodes) && count($childNodes) == 1) {
                $options = current($childNodes);

                if (is_array($options)) {
                    $options = $this->parseNodes($options);
                }

                $values[] = $this->newConstraint(key($childNodes), $options);
            } else {
                if (is_array($childNodes)) {
                    $childNodes = $this->parseNodes($childNodes);
                }

                $values[$name] = $childNodes;
            }
        }

        return $values;
    }

    /**
     * Loads the YAML class descriptions from the given file.
     *
     * @param string $path The path of the YAML file
     *
     * @return array|null The class descriptions or null, if the file was empty
     *
     * @throws \InvalidArgumentException If the file could not be loaded or did
     *                                   not contain a YAML array
     */
    private function parseFile($path)
    {
        $classes = $this->yamlParser->parse(file_get_contents($path));

        // empty file
        if (null === $classes) {
            return;
        }

        // not an array
        if (!is_array($classes)) {
            throw new \InvalidArgumentException(sprintf('The file "%s" must contain a YAML array.', $this->file));
        }

        return $classes;
    }

    /**
     * Loads the validation metadata from the given YAML class description.
     *
     * @param ClassMetadata $metadata         The metadata to load
     * @param array         $classDescription The YAML class description
     */
    private function loadClassMetadataFromYaml(ClassMetadata $metadata, array $classDescription)
    {
        if (isset($classDescription['constraints']) && is_array($classDescription['constraints'])) {
            foreach ($this->parseNodes($classDescription['constraints']) as $constraint) {
                $metadata->addConstraint($constraint);
            }
        }

        if (isset($classDescription['properties']) && is_array($classDescription['properties'])) {
            foreach ($classDescription['properties'] as $property => $constraints) {
                if (null !== $constraints) {
                    foreach ($this->parseNodes($constraints) as $constraint) {
                        $metadata->addPropertyConstraint($property, $constraint);
                    }
                }
            }
        }
    }
}
