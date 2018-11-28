<?php

namespace ITE\FormBundle\Controller\Component\Editable;

use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EntityController
 *
 * @Route("/ite-form/editable")
 */
class EntityController extends Controller
{
    /**
     * @Route("/edit", name="ite_form_component_editable_edit")
     */
    public function editAction(Request $request)
    {
        $editableManager = $this->get('ite_form.editable_manager');

        return $editableManager->handleRequest($request);
    }
}
