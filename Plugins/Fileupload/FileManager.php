<?php

namespace ITE\FormBundle\Plugins\Fileupload;

use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class FileManager
 * @package ITE\FormBundle\Plugins\Fileupload
 */
class FileManager
{
    /**
     * @var Request $request
     */
    protected $request;

    /**
     * @var array $config
     */
    protected $config;

    /**
     * @var array $options
     */
    protected $options;

    /**
     * @var Filesystem $fs
     */
    protected $fs;

    /**
     * @param Request $request
     * @param array $config
     * @param array $options
     */
    public function __construct(Request $request, array $config, array $options)
    {
        $this->request = $request;
        $this->config = $config;
        $this->options = $options;
        $this->fs = new Filesystem();
    }

    /**
     * @param $folder
     * @return array
     */
    public function getFiles($folder)
    {
        $prefix = !empty($this->config['prefix']) ? $this->config['prefix'] . '/' : '';
        $folderDir = $this->config['web_root'] . '/' . $prefix . $folder . '/';

        if (!is_dir($folderDir)) {
            return array();
        }

        $finder = new Finder();
        $finder->files()->in($folderDir)->sortByModifiedTime();

        $files = array();
        foreach ($finder as $file) {
            /** @var $file SplFileInfo */
            $files[] = new File($file->getRealPath());
        }

        return $files;
    }

    /**
     * @param $folder
     * @throws RuntimeException
     */
    public function removeFiles($folder)
    {
        $prefix = !empty($this->config['prefix']) ? $this->config['prefix'] . '/' : '';
        $folderDir = $this->config['web_root'] . '/' . $prefix . $folder . '/';

        if (!is_dir($folderDir)) {
            return;
        }

        $process = ProcessBuilder::create()
            ->setPrefix('rm')
            ->setArguments(array(
                '-rf',
                $folderDir,
            ))
            ->getProcess();
        $process->run();
        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }
    }

    /**
     * @param $from
     * @param $to
     * @param bool $removeFrom
     * @throws RuntimeException
     */
    public function syncFiles($from, $to, $removeFrom = false)
    {
        $prefix = !empty($this->config['prefix']) ? $this->config['prefix'] . '/' : '';
        $fromDir = $this->config['web_root'] . '/' . $prefix . $from . '/';
        $toDir = $this->config['web_root'] . '/' . $prefix . $to . '/';

        if (!is_dir($fromDir)) {
            return;
        }
        $this->fs->mkdir($toDir);

        $process = ProcessBuilder::create()
            ->setPrefix('rsync')
            ->setArguments(array(
                '-a',
                '--delete',
                $fromDir,
                $toDir
            ))
            ->getProcess();
        $process->run();
        if (!$process->isSuccessful()) {
            throw new RuntimeException($process->getErrorOutput());
        }

        if ($removeFrom) {
            $process = ProcessBuilder::create()
                ->setPrefix('rm')
                ->setArguments(array(
                    '-rf',
                    $fromDir,
                ))
                ->getProcess();
            $process->run();
            if (!$process->isSuccessful()) {
                throw new RuntimeException($process->getErrorOutput());
            }
        }
    }

    /**
     * @param $folder
     * @param array $options
     */
    public function handleFileUpload($folder, $options = array())
    {
//        $allowedExtensions = $options['allowed_extensions'];
//
//        // Build a regular expression like /\.(gif|jpe?g|png)$/i
//        $allowedExtensionsRegex = '/\.(' . implode('|', $allowedExtensions) . ')$/i';

        $prefix = !empty($this->config['prefix']) ? $this->config['prefix'] . '/' : '';
        $uploadDir = $this->config['web_root'] . '/' . $prefix . $folder . '/';
        $uploadUrl = $prefix . $folder . '/';

        $options = array_replace_recursive($this->options, $options, array(
            'upload_dir' => $uploadDir,
            'upload_url' => $uploadUrl ,
            'script_url' => $this->request->getUri(),
            'param_name' => $this->request->query->get('paramName')
//                'accept_file_types' => $allowedExtensionsRegex,
        ));

        $uploadHandler = new UploadHandler($options);

        // Without this Symfony will try to respond; the BlueImp upload handler class already did,
        // so it's time to hush up
        exit(0);
    }
}
