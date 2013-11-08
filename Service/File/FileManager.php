<?php

namespace ITE\FormBundle\Service\File;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class FileManager
 * @package ITE\FormBundle\Service\File
 */
class FileManager extends AbstractFileService implements FileManagerInterface
{
    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     *
     */
    public function __construct()
    {
        $this->fs = new Filesystem();
    }

    /**
     * @param string $ajaxToken
     * @param string $propertyPath
     * @return array<WebFile>
     */
    public function getFiles($ajaxToken, $propertyPath)
    {
        $propertyPath = md5($propertyPath);
        $absolutePath = $this->getAbsolutePath($ajaxToken, $propertyPath);
        if (!is_dir($absolutePath)) {
            return array();
        }

        $finder = new Finder();
        $finder->files()->in($absolutePath)->sortByChangedTime();

        $relativePath = $this->getRelativePath($ajaxToken, $propertyPath);

        return array_map(function($file) use ($relativePath) {
            /** @var $file SplFileInfo */
            $uri = $relativePath . '/' . $file->getBasename();

            return new WebFile($file->getRealPath(), $uri);
        }, iterator_to_array($finder, false));
    }

    /**
     * @param string $ajaxToken
     */
    public function removeFiles($ajaxToken)
    {
        $dir = $this->getAbsolutePath($ajaxToken);
        if (!is_dir($dir)) {
            return;
        }

        $this->fs->remove($dir);
    }

} 