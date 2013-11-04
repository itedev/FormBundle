<?php

namespace ITE\FormBundle\Service\File;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Interface FileManagerInterface
 * @package ITE\FormBundle\Service\File
 */
interface FileManagerInterface
{
    /**
     * @param string|array|null $dir
     * @return array<File>
     */
    public function getFiles($dir = null);

    /**
     * @param string|array|null $dir
     * @return void
     */
    public function removeFiles($dir = null);

    /**
     * @param string|array|null $dir
     * @return mixed
     */
    public function handleUpload($dir = null);
} 