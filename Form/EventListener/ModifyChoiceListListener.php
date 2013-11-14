<?php

namespace ITE\FormBundle\Form\EventListener;

use ITE\FormBundle\Form\ChoiceList\SimpleChoiceList;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ModifyChoiceListListener
 * @package ITE\FormBundle\Form\EventListener
 */
class ModifyChoiceListListener implements EventSubscriberInterface
{
    /**
     * @var ChoiceListInterface $choiceList
     */
    protected $choiceList;

    /**
     * @param ChoiceListInterface $choiceList
     */
    public function __construct(ChoiceListInterface $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
//        $form = $event->getForm();
//        $data = $event->getData();
//
//        $field = $form->getName();
//        $options = $form->getConfig()->getOptions();
//        $type = $form->getConfig()->getType()->getName();
//
//        $parent = $form->getParent();
//        $parent->add($field, $type, array_replace($options, array(
//            'choice_list' => new SimpleChoiceList(
//                    array_merge(
//                        $this->choiceList->getChoices(),
//                        array('4' => 'four')
//                    ),
//                    $options['preferred_choices']
//                )
//        )));
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit'
        );
    }
} 