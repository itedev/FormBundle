<?php

namespace ITE\FormBundle\Form\EventListener\Component\Hierarchical;

use ITE\FormBundle\Form\Builder\Event\HierarchicalEvent;
use ITE\FormBundle\Form\Builder\Event\Model\HierarchicalParent;
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
    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();

        if (null === $data = $form->getConfig()->getOption('hierarchical_data')) {
            return;
        }

        FormUtils::setData($form, $data);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        // priority should be greater than HierarchicalReferenceSubscriber::postSubmit priority

        return [
            FormEvents::POST_SUBMIT => ['postSubmit', -511],
        ];
    }

}