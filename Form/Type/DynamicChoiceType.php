<?php

namespace ITE\FormBundle\Form\Type;

use ITE\FormBundle\Form\ChoiceList\DynamicChoiceList;
use ITE\FormBundle\Form\DataTransformer\DynamicChoicesToValuesTransformer;
use ITE\FormBundle\Form\DataTransformer\DynamicChoiceToValueTransformer;
use ITE\FormBundle\SF\SFFormExtensionInterface;
use ITE\FormBundle\Util\ChoiceViewUtils;
use ITE\JsBundle\SF\SFInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class DynamicChoiceType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DynamicChoiceType extends AbstractType
{
    /**
     * @var RegistryInterface
     */
    private $registry;

    /**
     * @var SFInterface
     */
    private $sfForm;

    /**
     * @var array
     */
    private $choiceListCache = [];

    /**
     * @var array
     */
    private $choicesCache = [];

    /**
     * @param RegistryInterface $registry
     * @param SFFormExtensionInterface $sfForm
     */
    public function __construct(RegistryInterface $registry, SFFormExtensionInterface $sfForm)
    {
        $this->registry = $registry;
        $this->sfForm = $sfForm;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ('choice' === $options['widget']) {
            // choice
            if ($options['multiple']) {
                $builder->addViewTransformer(new DynamicChoicesToValuesTransformer($options['choice_list']));
            } else {
                $builder->addViewTransformer(new DynamicChoiceToValueTransformer($options['choice_list']));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ('choice' === $options['widget']) {
            // choice
            $choiceViews = $options['choice_list']->getViews();

            $data = $form->getData();
            $empty = null === $data || [] === $data;
            if (!$empty) {
                $options['choice_list']->setData($data, !$options['preload_choices']);
            }

            $dynamicChoiceDomainBag = $this->sfForm->getDynamicChoiceDomainBag();
            if (!$dynamicChoiceDomainBag->has($options['domain'])) {
                $dynamicChoiceDomainBag->set($options['domain'], ChoiceViewUtils::choiceViewsToChoices($choiceViews));
            }

            array_splice(
                $view->vars['block_prefixes'],
                array_search($this->getName(), $view->vars['block_prefixes']),
                0,
                [
                    'choice',
                ]
            );

            $view->vars = array_replace($view->vars, [
                'multiple' => $options['multiple'],
                'expanded' => false,
                'preferred_choices' => $options['choice_list']->getPreferredViews(),
                'choices' => $options['preload_choices'] ? $options['choice_list']->getRemainingViews() : [],
                'separator' => '-------------------',
                'placeholder' => null,
            ]);

            if ($options['multiple']) {
                $view->vars['is_selected'] = function ($choice, array $values) {
                    return in_array($choice, $values, true);
                };
            } else {
                $view->vars['is_selected'] = function ($choice, $value) {
                    return $choice === $value;
                };
            }

            $view->vars['placeholder_in_choices'] = 0 !== count($options['choice_list']->getChoicesForValues(['']));

            // Only add the empty value option if this is not the case
            if (null !== $options['placeholder'] && !$view->vars['placeholder_in_choices']) {
                $view->vars['placeholder'] = $options['placeholder'];
            }

            if ($options['multiple']) {
                $view->vars['full_name'] .= '[]';
            }
        }

        $view->vars['attr']['data-dynamic-choice-domain'] = $options['domain'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $registry = $this->registry;
        $choiceListCache = &$this->choiceListCache;
        $choicesCache = &$this->choicesCache;

        $choiceList = function (Options $options) use (&$choiceListCache) {
//            $domain = $options['domain'];
//
//            if (!isset($choiceListCache[$domain])) {
                $preferredChoices = array_slice($options['choices'], 0, $options['choice_limit'], true);

                return new DynamicChoiceList($options['choices'], $preferredChoices);
//                $choiceListCache[$domain] = new DynamicChoiceList($options['choices'], $preferredChoices);
//            }
//
//            return $choiceListCache[$domain];
        };

        $emptyData = function (Options $options) {
            if ($options['multiple']) {
                return [];
            }

            return '';
        };

        $placeholder = function (Options $options) {
            return $options['required'] ? null : '';
        };

        $choicesNormalizer = function (Options $options, $choices) use ($registry, &$choicesCache) {
            $domain = $options['domain'];

            if (!isset($choicesCache[$domain])) {
                $choices = null !== $options['choice_builder']
                    ? call_user_func_array($options['choice_builder'], [$registry])
                    : $choices;

                $choicesCache[$domain] = $choices;
            }

            return $choicesCache[$domain];
        };

        $placeholderNormalizer = function (Options $options, $placeholder) {
            if ($options['multiple']) {
                // never use an empty value for this case
                return;
            } elseif (false === $placeholder) {
                // an empty value should be added but the user decided otherwise
                return;
            }

            // empty value has been set explicitly
            return $placeholder;
        };

        $resolver->setRequired([
            'domain',
        ]);
        $resolver->setDefaults([
            'choice_builder' => null,
            'choice_limit' => 0,
            'preload_choices' => true,
            'multiple' => false,
            'choices' => [],
            'choice_list' => $choiceList,
            'empty_data' => $emptyData,
            'placeholder' => $placeholder,
            'error_bubbling' => false,
            'compound' => false,
            'data_class' => null,
            'choice_label' => null,
            'separator' => ',',
            'widget' => 'choice',
        ]);
        $resolver->setNormalizers([
            'placeholder' => $placeholderNormalizer,
            'choices' => $choicesNormalizer,
        ]);
        $resolver->setAllowedValues([
            'widget' => [
                'choice',
                'hidden',
            ],
        ]);
        $resolver->setAllowedTypes([
            'domain' => ['string'],
            'preload_choices' => ['bool'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_dynamic_choice';
    }
}
