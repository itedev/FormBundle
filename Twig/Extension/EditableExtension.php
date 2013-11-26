<?php

namespace ITE\FormBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Twig_Environment;
use Twig_Extension;
use Twig_Template;

/**
 * Class EditableExtension
 * @package ITE\FormBundle\Twig\Extension
 */
class EditableExtension extends Twig_Extension
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ite_editable', array($this, 'editable'), array()),
        );
    }

    /**
     * @param $entity
     * @param $field
     */
    public function editable($entity, $field)
    {
        /** @var $classMetadata ClassMetadataInfo */
        $classMetadata = $this->em->getClassMetadata(get_class($entity));

        if (!$classMetadata->hasField($field)) {

        }

        $fieldMapping = $classMetadata->getFieldMapping($field);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.editable_extension';
    }

}