<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\Form\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class ChoiceTypeExtension
 * @package ITE\FormBundle\Form\Extension
 */
class ChoiceTypeExtension
{
    /**
     * @var ChoiceType $choiceType
     */
    private $choiceType;

    /**
     * @param ChoiceType $choiceType
     */
    public function __construct(ChoiceType $choiceType)
    {
        $this->choiceType = $choiceType;
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
            }

            return $choiceListCache[$hash];
        };

        $resolver->setDefaults(array(
            'allow_add' => false,
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