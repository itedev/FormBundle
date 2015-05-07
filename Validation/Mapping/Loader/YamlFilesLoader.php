<?php

namespace ITE\FormBundle\Validation\Mapping\Loader;

/**
 * Loads validation metadata from a list of YAML files.
 *
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author c1tru55 <mr.c1tru55@gmail.com>
 *
 * @see FilesLoader
 */
class YamlFilesLoader extends FilesLoader
{
    /**
     * {@inheritdoc}
     */
    public function getFileLoaderInstance($file)
    {
        return new YamlFileLoader($file);
    }
}
