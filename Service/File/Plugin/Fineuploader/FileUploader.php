<?php

namespace ITE\FormBundle\Service\File\Plugin\Fineuploader;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class FileUploadEngine
 *
 * @deprecated
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FileUploader
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