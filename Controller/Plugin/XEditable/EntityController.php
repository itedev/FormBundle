<?php

namespace ITE\FormBundle\Controller\Plugin\XEditable;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FileController
 * @package ITE\FormBundle\Controller\Plugin\XEditable
 *
 * @Route("/ite-form/x-editable")
 */
class EntityController extends Controller
{
    /**
     * @Route("/edit", name="ite_form_plugin_x_editable_edit")
     */
    public function editAction(Request $request)
    {
        $class = $request->request->get('class');
        $identifier = $request->request->get('pk');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($class)->find($identifier);

        $field = $request->request->get('name');
        $value = $request->request->get('value');

        $form = $this->get('ite_form.editable_manager')->getForm($entity, $field);
        $form->submit(array(
            $field => $value
        ));
        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return new Response();
        }

        $errors = $form->getErrorsAsString();

        return new Response($errors, empty($errors) ? 200 : 400);
    }
}
