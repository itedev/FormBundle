<?php

namespace ITE\FormBundle\Service\File;

use ITE\FormBundle\Form\Extension\AjaxTokenFormTypeExtension;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;

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
     * @return array<File>
     */
    public function getFiles($ajaxToken, $propertyPath)
    {
        $dir = $this->getAbsolutePath($ajaxToken, $propertyPath);
        if (!is_dir($dir)) {
            return array();
        }

        $finder = new Finder();
        $finder->files()->in($dir)->sortByChangedTime();

        return array_map(function($file) {
            /** @var $file SplFileInfo */
            return new File($file->getRealPath());
        }, iterator_to_array($finder));
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