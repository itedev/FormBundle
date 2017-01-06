<?php

namespace ITE\FormBundle\Validation\Mapping\Loader;

use ITE\FormBundle\Validation\Mapping\ClassMetadata;
use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\Validator\Exception\MappingException;

/**
 * Loads validation metadata from an XML file.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class XmlFileLoader extends FileLoader
{
    /**
     * The XML nodes of the mapping file.
     *
     * @var \SimpleXMLElement[]|null
     */
    protected $classes;

    /**
     * {@inheritdoc}
     */
    public function loadClassMetadata(ClassMetadata $metadata)
    {
        if (null === $this->classes) {
            // This method may throw an exception. Do not modify the class'
            // state before it completes
            $xml = $this->parseFile($this->file);

            $this->classes = [];

            foreach ($xml->namespace as $namespace) {
                $this->addNamespaceAlias((string) $namespace['prefix'], trim((string) $namespace));
            }

            foreach ($xml->class as $class) {
                $this->classes[(string) $class['name']] = $class;
            }
        }

        if (isset($this->classes[$metadata->getClassName()])) {
            $classDescription = $this->classes[$metadata->getClassName()];

            $this->loadClassMetadataFromXml($metadata, $classDescription);

            return true;
        }

        return false;
    }

    /**
     * Parses a collection of "constraint" XML nodes.
     *
     * @param \SimpleXMLElement $nodes The XML nodes
     *
     * @return array The Constraint instances
     */
    protected function parseConstraints(\SimpleXMLElement $nodes)
    {
        $constraints = [];

        foreach ($nodes as $node) {
            if (count($node) > 0) {
                if (count($node->value) > 0) {
                    $options = $this->parseValues($node->value);
                } elseif (count($node->constraint) > 0) {
                    $options = $this->parseConstraints($node->constraint);
                } elseif (count($node->option) > 0) {
                    $options = $this->parseOptions($node->option);
                } else {
                    $options = [];
                }
            } elseif (strlen((string) $node) > 0) {
                $options = trim($node);
            } else {
                $options = null;
            }

            $constraints[] = $this->newConstraint((string) $node['name'], $options);
        }

        return $constraints;
    }

    /**
     * Parses a collection of "value" XML nodes.
     *
     * @param \SimpleXMLElement $nodes The XML nodes
     *
     * @return array The values
     */
    protected function parseValues(\SimpleXMLElement $nodes)
    {
        $values = [];

        foreach ($nodes as $node) {
            if (count($node) > 0) {
                if (count($node->value) > 0) {
                    $value = $this->parseValues($node->value);
                } elseif (count($node->constraint) > 0) {
                    $value = $this->parseConstraints($node->constraint);
                } else {
                    $value = [];
                }
            } else {
                $value = trim($node);
            }

            if (isset($node['key'])) {
                $values[(string) $node['key']] = $value;
            } else {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * Parses a collection of "option" XML nodes.
     *
     * @param \SimpleXMLElement $nodes The XML nodes
     *
     * @return array The options
     */
    protected function parseOptions(\SimpleXMLElement $nodes)
    {
        $options = [];

        foreach ($nodes as $node) {
            if (count($node) > 0) {
                if (count($node->value) > 0) {
                    $value = $this->parseValues($node->value);
                } elseif (count($node->constraint) > 0) {
                    $value = $this->parseConstraints($node->constraint);
                } else {
                    $value = [];
                }
            } else {
                $value = XmlUtils::phpize($node);
                if (is_string($value)) {
                    $value = trim($value);
                }
            }

            $options[(string) $node['name']] = $value;
        }

        return $options;
    }

    /**
     * Loads the XML class descriptions from the given file.
     *
     * @param string $path The path of the XML file
     *
     * @return \SimpleXMLElement The class descriptions
     *
     * @throws MappingException If the file could not be loaded
     */
    protected function parseFile($path)
    {
        try {
            $dom = XmlUtils::loadFile($path, __DIR__.'/schema/dic/constraint-mapping/constraint-mapping-1.0.xsd');
        } catch (\Exception $e) {
            throw new MappingException($e->getMessage(), $e->getCode(), $e);
        }

        return simplexml_import_dom($dom);
    }

    /**
     * Loads the validation metadata from the given XML class description.
     *
     * @param ClassMetadata $metadata         The metadata to load
     * @param array         $classDescription The XML class description
     */
    private function loadClassMetadataFromXml(ClassMetadata $metadata, $classDescription)
    {
        foreach ($this->parseConstraints($classDescription->constraint) as $constraint) {
            $metadata->addConstraint($constraint);
        }

        foreach ($classDescription->property as $property) {
            foreach ($this->parseConstraints($property->constraint) as $constraint) {
                $metadata->addPropertyConstraint((string) $property['name'], $constraint);
            }
        }
    }
}
