<?php

namespace ITE\FormBundle\Controller\Plugin\Fineuploader;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class FileController
 *
 * @Route("/ite-form/fineuploader")
 */
class FileController extends Controller
{
    /**
     * @Route("/upload", name="ite_form_plugin_fineuploader_file_upload")
     */
    public function uploadAction()
    {
        $response = $this->get('ite_form.fineuploader.file_uploader')->handleUpload();

        return $response;
    }
}
