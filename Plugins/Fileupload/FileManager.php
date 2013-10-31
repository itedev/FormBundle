<?php

namespace ITE\FormBundle\Plugins\Fileupload;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;

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
     * Get a list of files already present. The 'folder' option is required.
     * If you pass consistent options to this method and handleFileUpload with
     * regard to paths, then you will get consistent results.
     */
    public function getFiles($options = array())
    {
        return $this->options['file_manager']->getFiles($options);
    }

    /**
     * Remove the folder specified by 'folder' and its contents.
     * If you pass consistent options to this method and handleFileUpload with
     * regard to paths, then you will get consistent results.
     */
    public function removeFiles($options = array())
    {
        return $this->options['file_manager']->removeFiles($options);
    }

    /**
     * Sync existing files from one folder to another. The 'fromFolder' and 'toFolder'
     * options are required. As with the 'folder' option elsewhere, these are appended
     * to the file_base_path for you, missing parent folders are created, etc. If
     * 'fromFolder' does not exist no error is reported as this is common if no files
     * have been uploaded. If there are files and the sync reports errors an exception
     * is thrown.
     *
     * If you pass consistent options to this method and handleFileUpload with
     * regard to paths, then you will get consistent results.
     */
    public function syncFiles($options = array())
    {
        return $this->options['file_manager']->syncFiles($options);
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

        $uploadDir = $this->config['web_dir'] . '/' . $folder . '/';
        $uploadUrl = $this->config['web_url'] . '/' . $folder . '/';

        $options = array_replace_recursive($this->options, $options, array(
            'upload_dir' => $uploadDir,
            'upload_url' => $uploadUrl ,
            'script_url' => $this->request->getUri(),
//                'accept_file_types' => $allowedExtensionsRegex,
        ));

        $this->fs->mkdir($uploadDir);
        $uploadHandler = new UploadHandler($options);

        // From https://github.com/blueimp/jQuery-File-Upload/blob/master/server/php/index.php
        // There's lots of REST fanciness here to support different upload methods, so we're
        // keeping the blueimp implementation which goes straight to the PHP standard library.
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
                break;
            case 'HEAD':
            case 'GET':
                $uploadHandler->get();
                break;
            case 'POST':
                if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
                    $uploadHandler->delete();
                } else {
                    $uploadHandler->post();
                }
                break;
            case 'DELETE':
                $uploadHandler->delete();
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }

        // Without this Symfony will try to respond; the BlueImp upload handler class already did,
        // so it's time to hush up
        exit(0);
    }
}
