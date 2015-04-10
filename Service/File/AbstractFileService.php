<?php

namespace ITE\FormBundle\Service\File;

/**
 * Class AbstractFileService
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
abstract class AbstractFileService
{
    /**
     * @var string
     */
    protected $webRoot;

    /**
     * @var string
     */
    protected $tmpPrefix;

    /**
     * Set webRoot
     *
     * @param string $webRoot
     * @return FileManager
     */
    public function setWebRoot($webRoot)
    {
        $this->webRoot = rtrim($webRoot, '/');

        return $this;
    }

    /**
     * Set tmpPrefix
     *
     * @param string $tmpPrefix
     * @return FileManager
     */
    public function setTmpPrefix($tmpPrefix)
    {
        $this->tmpPrefix = trim($tmpPrefix, '/');

        return $this;
    }

    /**
     * @param string $ajaxToken
     * @param string|null $propertyPath
     * @return string
     */
    protected function getRelativePath($ajaxToken, $propertyPath = null)
    {
        return '/' . $this->tmpPrefix . '/' . $ajaxToken . (!empty($propertyPath) ? '/' . $propertyPath : '');
    }

    /**
     * @param string $ajaxToken
     * @param string|null $propertyPath
     * @return string
     */
    protected function getAbsolutePath($ajaxToken, $propertyPath = null)
    {
        return $this->webRoot . '/' . $this->tmpPrefix . '/' . $ajaxToken . (!empty($propertyPath) ? '/' . $propertyPath : '');
    }
} 