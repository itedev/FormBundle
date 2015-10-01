<?php

namespace ITE\FormBundle\Form\EventListener\Component\Hierarchical;

use ITE\FormBundle\Form\Builder\Event\HierarchicalEvent;
use ITE\FormBundle\Form\Builder\Event\Model\HierarchicalParent;
use ITE\FormBundle\Form\EventListener\HierarchicalFormEvents;
use ITE\FormBundle\FormAccess\FormAccess;
use ITE\FormBundle\FormAccess\FormAccessorInterface;
use ITE\FormBundle\Util\EventDispatcherUtils;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * Class HierarchicalSetDataSubscriber
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class HierarchicalSetDataSubscriber implements EventSubscriberInterface
{
    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if (!$form->getConfig()->hasOption('hierarchical_data')) {
            return;
        }

        $data = $form->getConfig()->getOption('hierarchical_data');
        FormUtils::setData($form, $data);
        $this->dispatchSetDataEventRecursive($form);
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        if (!$form->getConfig()->hasOption('hierarchical_data')) {
            return;
        }

        $data = $form->getConfig()->getOption('hierarchical_data');
        FormUtils::setData($form, $data);
        $this->dispatchSetDataEventRecursive($form);
    }

    /**
     * @param FormInterface $form
     */
    private function dispatchSetDataEventRecursive(FormInterface $form)
    {
        foreach ($form as $child) {
            $this->dispatchSetDataEventRecursive($child);
        }

        $event = new FormEvent($form, $form->getData());
        $ed = $form->getConfig()->getEventDispatcher();
        $ed->dispatch(HierarchicalFormEvents::POST_SET_DATA, $event);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        // priority should be greater than HierarchicalAddChildSubscriber priority

        return [
            FormEvents::POST_SET_DATA => ['postSetData', -511],
            FormEvents::POST_SUBMIT => ['postSubmit', -511],
        ];
    }
}
