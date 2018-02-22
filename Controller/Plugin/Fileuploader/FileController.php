<?php

namespace ITE\FormBundle\Controller\Plugin\Fileuploader;

use FileUploader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class FileController
 *
 * @Route("/ite-form/fileuploader/file")
 */
class FileController extends Controller
{
    /**
     * @Route("/upload", name="ite_form_plugin_fileuploader_file_upload")
     */
    public function uploadAction(Request $request)
    {
        $fileUploader = new FileUploader('files', [
            'uploadDir' => $this->container->getParameter('ite_form.component.ajax_file_upload.upload_dir') . '/',
            'uploadPath' => $this->container->getParameter('ite_form.component.ajax_file_upload.upload_path') . '/',
            'title' => 'name'
        ]);
//        foreach ($fileUploader->getRemovedFiles('file') as $key => $value) {
//            unlink('../uploads/' . $value['name']);
//        }
        $data = $fileUploader->upload();

        return new JsonResponse($data);
    }
}