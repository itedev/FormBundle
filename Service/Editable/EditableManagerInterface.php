<?php

namespace ITE\FormBundle\Service\Editable;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Form\Form;

/**
 * Interface EditableManagerInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface EditableManagerInterface
{
    /**
     * @param $entity
     * @param $field
     * @return Form
     */
    public function createForm($entity, $field);
} 