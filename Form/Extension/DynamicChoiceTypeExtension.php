<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\Form\ChoiceList\SimpleChoiceList;
use ITE\FormBundle\Form\EventListener\FixCheckboxInputListener;
use ITE\FormBundle\Form\EventListener\FixRadioInputListener;
use ITE\FormBundle\Form\EventListener\ModifyChoiceListListener;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\EventListener\FixCheckboxInputListener as BaseFixCheckboxInputListener;
use Symfony\Component\Form\Extension\Core\EventListener\FixRadioInputListener as BaseFixRadioInputListener;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DynamicChoiceTypeExtension.php
 * @package ITE\FormBundle\Form\Extension
 */
class DynamicChoiceTypeExtension extends AbstractTypeExtension
{
    /**
     * Caches created choice lists.
     * @var array
     */
    protected $choiceListCache = array();

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['allow_modify']) {
            return;
        }

        $builder->addEventSubscriber(new ModifyChoiceListListener($options['choice_list']));
        if ($options['expanded']) {
            $ed = $builder->getEventDispatcher();
            $listeners = $ed->getListeners(FormEvents::PRE_SUBMIT);
            if ($options['multiple']) {
                foreach ($listeners as $listener) {
                    if ($listener[0] instanceof BaseFixCheckboxInputListener) {
                        $ed->removeSubscriber($listener[0]);
                        break;
                    }
                }
                $builder->addEventSubscriber(new FixCheckboxInputListener($options['choice_list']), 10);
            } else {
                foreach ($listeners as $listener) {
                    if ($listener[0] instanceof BaseFixRadioInputListener) {
                        $ed->removeSubscriber($listener[0]);
                        break;
                    }
                }
                $builder->addEventSubscriber(new FixRadioInputListener($options['choice_list'], $builder->has('placeholder')), 10);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choiceListCache =& $this->choiceListCache;

        $choiceList = function (Options $options) use (&$choiceListCache) {
            // Harden against NULL values (like in EntityType and ModelType)
            $choices = null !== $options['choices'] ? $options['choices'] : array();

            // Reuse existing choice lists in order to increase performance
            $hash = md5(json_encode(array($choices, $options['preferred_choices'])));

            if (!isset($choiceListCache[$hash])) {
                $choiceListCache[$hash] = new SimpleChoiceList($choices, $options['preferred_choices']);
                if ($options['allow_modify']) {
                    $choiceListCache[$hash]->setAllowModify(true);
                }
            }

            return $choiceListCache[$hash];
        };

        $allowModify = function (Options $options) {
            return isset($options['hierarchical']) && !empty($options['hierarchical']) ? true : false;
        };

        $resolver->setDefaults(array(
            'allow_modify' => $allowModify,
            'choice_list' => $choiceList,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'choice';
    }
} 