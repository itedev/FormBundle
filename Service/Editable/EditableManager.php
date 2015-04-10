<?php

namespace ITE\FormBundle\Service\Editable;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class EditableManager
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class EditableManager implements EditableManagerInterface
{
    /**
     * @var FormFactoryInterface $formFactory
     */
    protected $formFactory;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param $entity
     * @param $field
     * @return Form
     */
    public function createForm($entity, $field)
    {
        return $this->getForm($entity, $field);
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