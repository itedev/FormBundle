<?php

namespace ITE\FormBundle\Form\EventListener;

use ITE\FormBundle\Form\ChoiceList\SimpleChoiceList;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class ExplodeCollectionListener
 * @package ITE\FormBundle\Form\EventListener
 */
class ExplodeCollectionListener implements EventSubscriberInterface
{
    protected $separator;

    /**
     * @param string $separator
     */
    public function __construct($separator = ',')
    {
        $this->separator = $separator;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        if (is_array($data) && !empty($data)) {
            $event->setData(explode($this->separator, current($data)));
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => array('preSubmit', 10),
        );
    }
} 