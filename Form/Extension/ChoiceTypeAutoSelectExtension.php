<?php

namespace ITE\FormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ChoiceTypeAutoSelectExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ChoiceTypeAutoSelectExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['auto_select']) {
            return;
        }
        if ($options['multiple'] || $options['expanded']) {
            return;
        }

        /** @var ChoiceListInterface $choiceList */
        $choiceList = $options['choice_list'];
        $choices = $choiceList->getChoices();

        if (1 === count($choices)) {
            $firstChoice = array_shift($choices);

            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($firstChoice) {
                if (null === $event->getData() || [] === $event->getData()) {
                    $event->setData($firstChoice);
                }
            });
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver): void
    {
        $resolver->setDefaults([
            'auto_select' => false,
        ]);
        $resolver->setAllowedTypes([
            'auto_select' => ['bool'],
        ]);
    }

    public function getExtendedType(): string
    {
        return 'choice';
    }
}
