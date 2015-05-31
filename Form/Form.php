<?php

namespace ITE\FormBundle\Form;

use ITE\FormBundle\Form\Builder\Event\HierarchicalEvent;
use ITE\FormBundle\Form\Builder\Event\Model\HierarchicalParent;
use ITE\FormBundle\Form\Builder\FormBuilder;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Form as BaseForm;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class Form
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Form extends BaseForm implements FormInterface
{
    /**
     * {@inheritdoc}
     */
    public function addHierarchical($child, $parents, $type = null, array $options = array(), $formModifier = null)
    {
        if (!is_string($parents) && !is_array($parents)) {
            throw new UnexpectedTypeException($parents, 'string or array');
        }
        if (empty($parents)) {
            throw new \InvalidArgumentException('You must set at least one parent');
        }
        if (!is_array($parents)) {
            $parents = array($parents);
        }
        if (!is_callable($formModifier)) {
            throw new \InvalidArgumentException('The form modifier handler must be a valid PHP callable.');
        }

        $options = array_merge($options, [
            'hierarchical_parents' => $parents,
        ]);

        /** @var FormBuilder $config */
        $config = $this->getConfig();
        $formAccessor = $config->getFormAccessor();

        parent::add($child, $type, $options);

        FormUtils::addEventListener($this->get($child), FormEvents::PRE_SUBMIT, function(FormEvent $event)
        use ($child, $type, $options, $parents, $formModifier, $formAccessor) {
            $form = $event->getForm()->getParent();

            $rootForm = $form->getRoot();
            $originator = $rootForm->getConfig()->getAttribute('hierarchical_originator');

            $hierarchicalParents = [];
            $parentDatas = [];
            foreach ($parents as $parentName) {
                $parentForm = $formAccessor->getForm($form, $parentName);
                $parentData = $parentForm ? $parentForm->getData() : null;

                $hierarchicalParent = new HierarchicalParent($parentName, $parentData, $parentForm, $originator);
                $hierarchicalParents[$parentName] = $hierarchicalParent;
                $parentDatas[$parentName] = $parentData;
            }

            $hierarchicalEvent = new HierarchicalEvent($form, $hierarchicalParents, $options, true, $originator);

            $params = $parentDatas;
            array_unshift($params, $hierarchicalEvent);

            if (false === call_user_func_array($formModifier, $params)) {
                return;
            }

//            $ed = $form->get($child)->getConfig()->getEventDispatcher();
            $form->add($child, $type, $hierarchicalEvent->getOptions());
//            FormUtils::setEventDispatcher($form->get($child), $ed);
        })
        ;

        return $this;
    }
}