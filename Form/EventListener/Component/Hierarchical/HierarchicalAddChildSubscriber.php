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
 * Class HierarchicalAddChildSubscriber
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class HierarchicalAddChildSubscriber implements EventSubscriberInterface
{
    /**
     * @var string $child
     */
    private $child;

    /**
     * @var string $type
     */
    private $type;

    /**
     * @var array $options
     */
    private $options;

    /**
     * @var array $parents
     */
    private $parents;

    /**
     * @var callable $formModifier
     */
    private $formModifier;

    /**
     * @var bool $referenceLevelUp
     */
    private $referenceLevelUp;

    /**
     * @var FormAccessorInterface $formAccessor
     */
    private $formAccessor;

    /**
     * @var array $formHashes
     */
    private $formHashes = [];

    /**
     * @param string $child
     * @param string $type
     * @param array $options
     * @param array $parents
     * @param callable $formModifier
     * @param bool $referenceLevelUp
     * @param FormAccessorInterface|null $formAccessor
     */
    public function __construct(
        $child,
        $type,
        array $options,
        array $parents,
        $formModifier,
        $referenceLevelUp,
        FormAccessorInterface $formAccessor = null
    ) {
        $this->child = $child;
        $this->type = $type;
        $this->options = $options;
        $this->parents = $parents;
        $this->formModifier = $formModifier;
        $this->referenceLevelUp = $referenceLevelUp;
        $this->formAccessor = $formAccessor ? $formAccessor : FormAccess::createFormAccessor();
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        /** @var FormInterface $form */
        $form = $this->referenceLevelUp ? $event->getForm() : $event->getForm()->getParent();

        if (!$form->has($this->child)) {
            return;
        }

        $formHash = spl_object_hash($form->get($this->child));
        if (in_array($formHash, $this->formHashes) || $form->isSubmitted()) {
            return;
        }

        $hierarchicalParents = [];
        $parentDatas = [];
        foreach ($this->parents as $parentName) {
            $parentForm = $this->formAccessor->getForm($form, $parentName);
            $parentData = $parentForm ? $parentForm->getData() : null;

            $hierarchicalParent = new HierarchicalParent($parentName, $parentData, $parentForm);
            $hierarchicalParents[$parentName] = $hierarchicalParent;
            $parentDatas[$parentName] = $parentData;
        }

        $hierarchicalEvent = new HierarchicalEvent($form, $hierarchicalParents, $this->options);

        $params = $parentDatas;
        array_unshift($params, $hierarchicalEvent);

        if (false === call_user_func_array($this->formModifier, $params)) {
            // save old form hash
            $this->formHashes[] = $formHash;

            return;
        }

        $oldEd = $form->get($this->child)->getConfig()->getEventDispatcher();
        $form->add($this->child, $this->type, $hierarchicalEvent->getOptions());
        $newEd = $form->get($this->child)->getConfig()->getEventDispatcher();
        EventDispatcherUtils::extend($newEd, $oldEd);

        // save new form hash
        $this->formHashes[] = spl_object_hash($form->get($this->child));
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        /** @var FormInterface $form */
        $form = $this->referenceLevelUp ? $event->getForm() : $event->getForm()->getParent();

        if (!$form->has($this->child)) {
            return;
        }

        $formHash = spl_object_hash($form->get($this->child));

        $rootForm = $form->getRoot();
        $originator = $rootForm->getConfig()->getAttribute('hierarchical_originator');

        $hierarchicalParents = [];
        $parentDatas = [];
        foreach ($this->parents as $parentName) {
            $parentForm = $this->formAccessor->getForm($form, $parentName);
            $parentData = $parentForm ? $parentForm->getData() : null;

            $parentFullName = FormUtils::getFullName($parentForm);
            $isParentOriginator = null !== $originator
                ? in_array($parentFullName, $originator)
                : false;

            $hierarchicalParent = new HierarchicalParent($parentName, $parentData, $parentForm, $isParentOriginator);
            $hierarchicalParents[$parentName] = $hierarchicalParent;
            $parentDatas[$parentName] = $parentData;
        }

        $hierarchicalEvent = new HierarchicalEvent($form, $hierarchicalParents, $this->options, true, $originator);

        $params = $parentDatas;
        array_unshift($params, $hierarchicalEvent);

        if (false === call_user_func_array($this->formModifier, $params)) {
            // save old form hash
            $this->formHashes[] = $formHash;

            FormUtils::setOption($form->get($this->child), 'hierarchical_changed', false);

            return;
        }

        $oldEd = $form->get($this->child)->getConfig()->getEventDispatcher();
        $form->add($this->child, $this->type, $hierarchicalEvent->getOptions());
        $newEd = $form->get($this->child)->getConfig()->getEventDispatcher();
        EventDispatcherUtils::extend($newEd, $oldEd);

        // save new form hash
        $this->formHashes[] = spl_object_hash($form->get($this->child));
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => ['postSetData', -512],
            FormEvents::POST_SUBMIT => ['postSubmit', -512],
        ];
    }
}
