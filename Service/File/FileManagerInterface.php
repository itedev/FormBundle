<?php

namespace ITE\FormBundle\Service\File;

/**
 * Interface FileManagerInterface
 * @package ITE\FormBundle\Service\File
 */
interface FileManagerInterface
{
    /**
     * @param string $ajaxToken
     * @param string $propertyPath
     * @return array<WebFile>
     */
    public function getFiles($ajaxToken, $propertyPath);

    /**
     * @param string $ajaxToken
     */
    public function removeFiles($ajaxToken);
} 