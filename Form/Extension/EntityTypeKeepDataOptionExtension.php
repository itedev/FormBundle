<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\Form\ChoiceList\DynamicEntityChoiceList;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class EntityTypeKeepDataOptionExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class EntityTypeKeepDataOptionExtension extends AbstractTypeExtension
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var array
     */
    private $choiceListCache = [];

    public function __construct(PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['keep_data_option']) {
            return;
        }
        if ($options['expanded']) {
            return;
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            $data = $event->getData();
            /** @var DynamicEntityChoiceList $choiceList */
            $choiceList = $options['choice_list'];

            if (null !== $data) {
                $choiceList->addDataChoices($data);
            }
        }, -255);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver): void
    {
        $choiceListCache = &$this->choiceListCache;
        $propertyAccessor = $this->propertyAccessor;

        $choiceList = function (Options $options) use (&$choiceListCache, $propertyAccessor) {
            // Support for closures
            $propertyHash = is_object($options['property'])
                ? spl_object_hash($options['property'])
                : $options['property'];

            $choiceHashes = $options['choices'];

            // Support for recursive arrays
            if (is_array($choiceHashes)) {
                // A second parameter ($key) is passed, so we cannot use
                // spl_object_hash() directly (which strictly requires
                // one parameter)
                array_walk_recursive($choiceHashes, function (&$value) {
                    $value = spl_object_hash($value);
                });
            } elseif ($choiceHashes instanceof \Traversable) {
                $hashes = [];
                foreach ($choiceHashes as $value) {
                    $hashes[] = spl_object_hash($value);
                }

                $choiceHashes = $hashes;
            }

            $preferredChoiceHashes = $options['preferred_choices'];

            if (is_array($preferredChoiceHashes)) {
                array_walk_recursive($preferredChoiceHashes, function (&$value) {
                    $value = spl_object_hash($value);
                });
            }

            // Support for custom loaders (with query builders)
            $loaderHash = is_object($options['loader'])
                ? spl_object_hash($options['loader'])
                : $options['loader'];

            // Support for closures
            $groupByHash = is_object($options['group_by'])
                ? spl_object_hash($options['group_by'])
                : $options['group_by'];

            $hash = hash('sha256', json_encode([
                spl_object_hash($options['em']),
                $options['class'],
                $propertyHash,
                $loaderHash,
                $choiceHashes,
                $preferredChoiceHashes,
                $groupByHash,
            ]));

            if (!isset($choiceListCache[$hash])) {
                $choiceListCache[$hash] = new DynamicEntityChoiceList(
                    $options['em'],
                    $options['class'],
                    $options['property'],
                    $options['loader'],
                    $options['choices'],
                    $options['preferred_choices'],
                    $options['group_by'],
                    $propertyAccessor
                );
            }

            return $choiceListCache[$hash];
        };

        $resolver->setDefaults([
            'keep_data_option' => false,
            'choice_list' => $choiceList,
        ]);
        $resolver->setAllowedTypes([
            'keep_data_option' => ['bool'],
        ]);
    }

    public function getExtendedType(): string
    {
        return 'entity';
    }
}
