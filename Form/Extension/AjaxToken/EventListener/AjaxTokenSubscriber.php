<?php

namespace ITE\FormBundle\Form\Extension\AjaxToken\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Class AjaxTokenSubscriber
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxTokenSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $fieldName;

    /**
     * @param $fieldName
     */
    public function __construct($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($form->isRoot() && $form->getConfig()->getOption('compound')) {
            if (is_array($data)) {
                unset($data[$this->fieldName]);
            }
        }

        $event->setData($data);
    }
}
