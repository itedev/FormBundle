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
class FileManager implements FileManagerInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $webRoot;

    /**
     * @var string
     */
    protected $tmpPrefix;

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
     * Set request
     *
     * @param Request $request
     * @return FileManager
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

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
     * @param string|array|null $dir
     * @return array
     */
    public function getFiles($dir = null)
    {
        $dir = $this->getAbsoluteDir($dir);
        if (!is_dir($dir)) {
            return array();
        }

        $finder = new Finder();
        $finder->files()->in($dir);

        $files = array();
        foreach ($finder as $file) {
            /** @var $file SplFileInfo */
            $files[] = new File($file->getRealPath());
        }

        return $files;
    }

    /**
     * @param string|array|null $dir
     * @return void
     */
    public function removeFiles($dir = null)
    {
        $dir = $this->getAbsoluteDir($dir);
        if (!is_dir($dir)) {
            return;
        }

        $this->fs->remove($dir);
    }

//    /**
//     * @param $from
//     * @param $to
//     * @param bool $removeFrom
//     * @throws RuntimeException
//     */
//    public function syncFiles($from, $to, $removeFrom = false)
//    {
//        $fromDir = $this->webRoot . '/' . $from . '/';
//        $toDir = $this->webRoot . '/' . $to . '/';
//
//        if (!is_dir($fromDir)) {
//            return;
//        }
//        $this->fs->mkdir($toDir);
//
//        $process = ProcessBuilder::create()
//          ->setPrefix('rsync')
//          ->setArguments(array(
//                  '-a',
//                  '--delete',
//                  $fromDir,
//                  $toDir
//              ))
//          ->getProcess();
//        $process->run();
//        if (!$process->isSuccessful()) {
//            throw new RuntimeException($process->getErrorOutput());
//        }
//
//        if ($removeFrom) {
//            $process = ProcessBuilder::create()
//              ->setPrefix('rm')
//              ->setArguments(array(
//                      '-rf',
//                      $fromDir,
//                  ))
//              ->getProcess();
//            $process->run();
//            if (!$process->isSuccessful()) {
//                throw new RuntimeException($process->getErrorOutput());
//            }
//        }
//    }

    /**
     * @param string|array|null $dir
     * @return mixed
     */
    public function handleUpload($dir = null) {}

    /**
     * @param null $ajaxToken
     * @param null $propertyPath
     * @return string
     */
    protected function getAjaxTempDir($ajaxToken = null, $propertyPath = null)
    {
        $ajaxToken = isset($ajaxToken)
          ? $ajaxToken
          : $this->request->query->get(AjaxTokenFormTypeExtension::DEFAULT_AJAX_TOKEN_FIELD_NAME);

        $propertyPath = isset($propertyPath)
          ? $propertyPath
          : $this->request->query->get('propertyPath');

        return $this->tmpPrefix . '/' . $ajaxToken . (isset($propertyPath) ? '/' . $propertyPath : '');
    }

    /**
     * @param string|array|null $dir
     * @return string
     */
    protected function getAbsoluteDir($dir = null)
    {
        if (!isset($dir) || is_array($dir)) {
            $ajaxToken = is_array($dir) && isset($dir[0]) ? $dir[0] : null;
            $propertyPath = is_array($dir) && isset($dir[1]) ? $dir[1] : null;

            return $this->webRoot . '/' . $this->getAjaxTempDir($ajaxToken, $propertyPath);
        }

        return $this->webRoot . '/' . trim($dir, '/');
    }
} 