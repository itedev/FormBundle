<?php

namespace ITE\FormBundle\Service\File;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Class WebFile
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class WebFile extends File
{
    /**
     * @var string $uri
     */
    protected $uri;

    /**
     * @param string $path
     * @param bool $uri
     */
    public function __construct($path, $uri)
    {
        $this->uri = $uri;
        parent::__construct($path);
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
} 