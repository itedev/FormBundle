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
     * @param string $ajaxToken
     * @param string $propertyPath
     * @return array<File>
     */
    public function getFiles($ajaxToken, $propertyPath);

    /**
     * @param string $ajaxToken
     */
    public function removeFiles($ajaxToken);
} 