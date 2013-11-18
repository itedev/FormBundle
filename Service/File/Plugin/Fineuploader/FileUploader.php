<?php

namespace ITE\FormBundle\Service\File\Plugin\Fineuploader;

use ITE\FormBundle\Service\File\FileUploader as BaseFileUploader;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class FileUploadEngine
 * @package ITE\FormBundle\Service\File\Plugin\Fineuploader
 */
class FileUploader extends BaseFileUploader
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        $this->options = $options;
        parent::__construct();
    }

    /**
     * Set options
     *
     * @param array $options
     * @return FileUploader
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $absolutePath
     * @param string $relativePath
     * @return void
     */
    public function upload($absolutePath, $relativePath)
    {
        $this->fs->mkdir($absolutePath);

        $uploadHandler = new UploadHandler();
        $uploadHandler->inputName = $this->request->query->get('inputName');

        $data = $uploadHandler->handleUpload($absolutePath);
        $data['uploadName'] = $uploadHandler->getUploadName();

        return new JsonResponse($data);
    }
} 