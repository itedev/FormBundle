<?php

namespace ITE\FormBundle\Form\EventListener;

use ITE\FormBundle\Form\ChoiceList\SimpleChoiceList;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;
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
        $form = $event->getForm();
        $data = $event->getData();

        $this->choiceList->setNewValuesFromData($data);

        $options = $form->getConfig()->getOptions();
        if (!$options['expanded']) {
            return;
        }

        $newValues = $this->choiceList->getNewValues();
        $remainingViews = $this->choiceList->getRemainingViews();
        foreach ($newValues as $i => $value) {
            /** @var $choiceView ChoiceView */
            $choiceView = $remainingViews[$i];

            $choiceOpts = array(
                'value'              => $choiceView->value,
                'label'              => $choiceView->label,
                'translation_domain' => $options['translation_domain'],
            );

            if ($options['multiple']) {
                $choiceType = 'checkbox';
                $choiceOpts['required'] = false;
            } else {
                $choiceType = 'radio';
            }

            $form->add($i, $choiceType, $choiceOpts);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        $form = $event->getForm();

        $options = $form->getConfig()->getOptions();
        if (!$options['expanded']) {
            return;
        }

        // remove dynamically added child checkboxes/radios
        $newValues = $this->choiceList->getNewValues();
        foreach ($newValues as $i => $newValue) {
            $child = $form->get($i);

            // change 'submitted' field value to false to prevent 'You cannot remove children from a submitted form' error
            $refObj = new \ReflectionObject($child);
            $refProp = $refObj->getProperty('submitted');
            $refProp->setAccessible(true);
            $refProp->setValue($child, false);

            $form->remove($i);
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::SUBMIT     => 'submit'
        );
    }
} 