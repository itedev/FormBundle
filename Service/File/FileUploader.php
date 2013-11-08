<?php

namespace ITE\FormBundle\Service\File;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FileUploader
 * @package ITE\FormBundle\Service\File
 */
abstract class FileUploader extends AbstractFileService implements FileUploaderInterface
{
    /**
     * @var Request
     */
    protected $request;

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
     * @return FileUploader
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     *
     */
    public function handleUpload()
    {
        $ajaxToken = $this->request->query->get('ajaxToken');
        $propertyPath = md5($this->request->query->get('propertyPath'));

        $absolutePath = $this->getAbsolutePath($ajaxToken, $propertyPath);
        $relativePath = $this->getRelativePath($ajaxToken, $propertyPath);

        $multiple = $this->request->get('multiple') ? true : false;
        if (!$multiple) {
            $this->fs->remove($absolutePath);
        }

        $this->upload($absolutePath, $relativePath);
    }

    /**
     * @param $absolutePath
     * @param $relativePath
     * @return mixed
     */
    abstract protected function upload($absolutePath, $relativePath);
} 