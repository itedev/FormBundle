<?php

namespace ITE\FormBundle\Validation\Mapping\Loader;

use Symfony\Component\Validator\Exception\MappingException;

/**
 * Base loader for loading validation metadata from a file.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 *
 * @see YamlFileLoader
 * @see XmlFileLoader
 */
abstract class FileLoader extends AbstractLoader
{
    /**
     * The file to load.
     *
     * @var string
     */
    protected $file;

    /**
     * Creates a new loader.
     *
     * @param string $file The mapping file to load
     *
     * @throws MappingException If the file does not exist or is not readable
     */
    public function __construct($file)
    {
        if (!is_file($file)) {
            throw new MappingException(sprintf('The mapping file "%s" does not exist', $file));
        }

        if (!is_readable($file)) {
            throw new MappingException(sprintf('The mapping file "%s" is not readable', $file));
        }

        if (!stream_is_local($this->file)) {
            throw new MappingException(sprintf('The mapping file "%s" is not a local file', $file));
        }

        $this->file = $file;
    }
}
