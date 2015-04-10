<?php

namespace ITE\FormBundle\Service\File;

/**
 * Interface FileManagerInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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