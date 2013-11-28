<?php

namespace ITE\FormBundle\Service\Editable;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use ITE\FormBundle\Annotation\Editable;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class EditableManager
 * @package ITE\FormBundle\Service\Editable
 */
class EditableManager implements EditableManagerInterface
{
    const EDITABLE_ANNOTATION = '\ITE\FormBundle\Annotation\Editable';

    /**
     * @var Reader $reader
     */
    protected $reader;

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var FormFactoryInterface $formFactory
     */
    protected $formFactory;

    /**
     * @param Reader $reader
     * @param EntityManager $em
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(Reader $reader, EntityManager $em, FormFactoryInterface $formFactory)
    {
        $this->reader = $reader;
        $this->em = $em;
        $this->formFactory = $formFactory;
    }

    /**
     * @param $class
     * @return ClassMetadata
     */
    public function getClassMetadata($class)
    {
        return $this->em->getClassMetadata($class);
    }

    /**
     * @param $entity
     * @param $field
     * @return Form
     */
    public function createForm($entity, $field)
    {
        /** @var $classMetadata ClassMetadataInfo */
        $classMetadata = $this->getClassMetadata(get_class($entity));
        $property = $classMetadata->getReflectionProperty($field);

        $editableAnnotation = $this->reader->getPropertyAnnotation($property, self::EDITABLE_ANNOTATION);
        /** @var $editableAnnotation Editable */
        if (!$editableAnnotation) {
            return $this->getForm($entity, $field);
        }

        return $this->getForm($entity, $field, $editableAnnotation->getType(), $editableAnnotation->getOptions());
    }

    /**
     * @param $entity
     * @param $field
     * @param $value
     * @return Form
     */
    public function createAndSubmitForm($entity, $field, $value)
    {
        $form = $this->createForm($entity, $field);
        $form->submit(array(
            $field => $value
        ));

        return $form;
    }

    /**
     * @param $entity
     * @param $field
     * @param null $type
     * @param array $options
     * @return Form
     */
    protected function getForm($entity, $field, $type = null, $options = array())
    {
        return $this->formFactory->createBuilder('form', $entity, array(
            'data_class' => get_class($entity),
            'csrf_protection' => false,
        ))
            ->add($field, $type, $options)
            ->getForm();
    }


} 