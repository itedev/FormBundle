<?php

namespace ITE\FormBundle\Service\File\Plugin\Fileupload;

use ITE\FormBundle\Service\File\FileManager as BaseFileManager;

/**
 * Class FileManager
 * @package ITE\FormBundle\Service\File\Plugin\Fileupload
 */
class FileManager extends BaseFileManager
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param $options
     */
    public function __construct($options)
    {
        parent::__construct();
        $this->options = $options;
    }

    /**
     * @param null $dir
     * @return mixed|void
     */
    public function handleUpload($dir = null)
    {
        $options = func_num_args() > 1 && is_array(func_get_arg(1)) ? func_get_arg(1) : array();

        $uploadDir = $this->getAbsoluteDir($dir) . '/';
        $uploadUrl = trim($dir, '/') . '/';

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
