<?php

namespace ITE\FormBundle\Controller\Plugin\Fileupload;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class FileController
 * @package ITE\FormBundle\Controller\Plugin\Fileupload
 *
 * @Route("/ite-form/fileupload")
 */
class FileController extends Controller
{
    /**
     * @Route("/upload", name="ite_form_plugin_fileupload_file_upload")
     */
    public function uploadAction()
    {
        $this->get('ite_form.fileupload.file_uploader')->handleUpload();
    }
}
