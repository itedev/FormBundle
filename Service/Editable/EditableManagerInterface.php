<?php

namespace ITE\FormBundle\Service\Editable;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Form\Form;

/**
 * Interface EditableManagerInterface
 * @package ITE\FormBundle\Service\Editable
 */
interface EditableManagerInterface
{
    /**
     * @param $entity
     * @param $field
     * @return Form
     */
    public function getForm($entity, $field);

    /**
     * @param $entity
     * @param $field
     * @param null $type
     * @param array $options
     * @return Form
     */
    public function createForm($entity, $field, $type = null, $options = array());

    /**
     * @param $class
     * @return ClassMetadata
     */
    public function getClassMetadata($class);
} 