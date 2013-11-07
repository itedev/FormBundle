<?php

namespace ITE\FormBundle\Service\File\Plugin\Fileupload;

use ITE\FormBundle\Service\File\FileUploader as BaseFileUploader;

/**
 * Class FileUploadEngine
 * @package ITE\FormBundle\Service\File\Plugin\Fileupload
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
    public function __construct($options)
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
        $options = array_replace_recursive($this->options, array(
            'upload_dir' => $absolutePath . '/',
            'upload_url' => $relativePath . '/' ,
            'script_url' => $this->request->getUri(),
            'param_name' => $this->request->query->get('paramName')
        ));

        $uploadHandler = new UploadHandler($options);

        exit(0);
    }
} 