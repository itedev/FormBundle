<?php

namespace ITE\FormBundle\Component\Editable;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface EditableManagerInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface EditableManagerInterface
{
    /**
     * @param object $entity
     * @param string $field
     * @param array $options
     * @return string
     */
    public function getWidget($entity, $field, array $options = []);

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function handleRequest(Request $request);

    /**
     * @param object $entity
     * @param string $field
     * @param array $options
     * @return FormInterface
     */
    public function createForm($entity, $field, array $options = []);
}
