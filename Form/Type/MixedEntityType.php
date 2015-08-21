<?php

namespace ITE\FormBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use ITE\FormBundle\Form\ChoiceList\MixedEntityChoiceList;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Component\Form\Exception\RuntimeException;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\ChoiceToValueTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class MixedEntityType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MixedEntityType extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @var array
     */
    private $entityChoiceListCache = [];

    /**
     * @var array
     */
    private $mixedEntityChoiceListCache = [];

    /**
     * @param ManagerRegistry $registry
     * @param PropertyAccessorInterface|null $propertyAccessor
     */
    public function __construct(ManagerRegistry $registry, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->registry = $registry;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new ChoiceToValueTransformer($options['choice_list']));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            0,
            [
                'choice',
                'entity'
            ]
        );

        $view->vars = array_replace($view->vars, array(
            'multiple' => false,
            'expanded' => false,
            'preferred_choices' => $options['choice_list']->getPreferredViews(),
            'choices' => $options['choice_list']->getRemainingViews(),
            'separator' => '-------------------',
            'placeholder' => null,
        ));

        $view->vars['is_selected'] = function($choice, $value) {
            return $choice === $value;
        };

        // Check if the choices already contain the empty value
        $view->vars['placeholder_in_choices'] = false; // 0 !== count($options['choice_list']->getChoicesForValues(['']));

        // Only add the empty value option if this is not the case
        if (null !== $options['placeholder'] && !$view->vars['placeholder_in_choices']) {
            $view->vars['placeholder'] = $options['placeholder'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $entityChoiceListCache = &$this->entityChoiceListCache;
        $mixedEntityChoiceListCache = &$this->mixedEntityChoiceListCache;
        $registry = $this->registry;
        $propertyAccessor = $this->propertyAccessor;
        $type = $this;

        $choiceList = function(Options $options) use (&$entityChoiceListCache, &$mixedEntityChoiceListCache, $type, $propertyAccessor) {
            $em = $options['em'];
            $entitiesOptions = $options['options'];

            $entityChoicesLists = [];
            $entityLabels = [];
            $entityChoiceListHashes = [];
            foreach ($entitiesOptions as $alias => $entityOptions) {
                $class = $entityOptions['class'];
                $queryBuilder = $entityOptions['query_builder'];
                $loader = $entityOptions['loader'];
                $property = $entityOptions['property'];
                $label = $entityOptions['label'];
                $choices = $entityOptions['choices'];
                $preferredChoices = $entityOptions['preferred_choices'];

                $qb = isset($queryBuilder)
                    ? $queryBuilder
                    : $em->getRepository($class)->createQueryBuilder('e');

                $defaultLoader = $type->getLoader($em, $qb, $class);
                $loader = isset($loader) ? $loader : $defaultLoader;

                $propertyHash = is_object($property)
                    ? spl_object_hash($property)
                    : $property;

                $choiceHashes = [];
                if (is_array($choices) || $choices instanceof \Traversable) {
                    foreach ($choices as $value) {
                        $choiceHashes[] = spl_object_hash($value);
                    }
                }

                $preferredChoiceHashes = [];
                if (is_array($preferredChoices)) {
                    foreach ($preferredChoices as $value) {
                        $preferredChoiceHashes[] = spl_object_hash($value);
                    }
                }

                $loaderHash = is_object($loader)
                    ? spl_object_hash($loader)
                    : $loader;

                $entityChoiceListHash = hash('sha256', json_encode([
                    spl_object_hash($em),
                    $class,
                    $propertyHash,
                    $loaderHash,
                    $choiceHashes,
                    $preferredChoiceHashes
                ]));

                if (!isset($entityChoiceListCache[$entityChoiceListHash])) {
                    $entityChoiceListCache[$entityChoiceListHash] = new EntityChoiceList(
                        $em,
                        $class,
                        $property,
                        $loader,
                        $choices,
                        $preferredChoices,
                        null,
                        $propertyAccessor
                    );
                }

                $entityChoiceListHashes[] = $entityChoiceListHash;

                $entityChoicesLists[$alias] = $entityChoiceListCache[$entityChoiceListHash];
                $entityLabels[$alias] = $label;
            }

            $mixedEntityChoiceListHash = implode(',', $entityChoiceListHashes);
            if (!isset($mixedEntityChoiceListCache[$mixedEntityChoiceListHash])) {
                $mixedEntityChoiceListCache[$mixedEntityChoiceListHash] = new MixedEntityChoiceList(
                    $entityChoicesLists,
                    $entityLabels
                );
            }

            return $mixedEntityChoiceListCache[$mixedEntityChoiceListHash];
        };

        $placeholder = function(Options $options) {
            return $options['required'] ? null : '';
        };

        $emNormalizer = function(Options $options, $em) use ($registry) {
            /* @var ManagerRegistry $registry */
            if (null !== $em) {
                if ($em instanceof ObjectManager) {
                    return $em;
                }

                return $registry->getManager($em);
            }

            $entitiesOptions = $options['options'];
            $firstEntityOptions = current($entitiesOptions);
            $firstEntityClass = $firstEntityOptions['class'];

            $em = $registry->getManagerForClass($firstEntityClass);
            if (null === $em) {
                throw new RuntimeException(sprintf(
                    'Class "%s" seems not to be a managed Doctrine entity. '.
                    'Did you forget to map it?',
                    $firstEntityClass
                ));
            }

            return $em;
        };

        $optionsNormalizers = function(Options $options, $entitiesOptions) {
            $normalizedOptions = [];
            foreach ($entitiesOptions as $alias => $entityOptions) {
                if (!isset($entityOptions['class'])) {
                    throw new MissingOptionsException('The required sub option "class" is missing.');
                }

                $class = $entityOptions['class'];
                $queryBuilder = isset($entityOptions['query_builder']) ? $entityOptions['query_builder'] : null;
                $loader = isset($entityOptions['loader']) ? $entityOptions['loader'] : null;
                $label = isset($entityOptions['label']) ? $entityOptions['label'] : null;
                $property = isset($entityOptions['property']) ? $entityOptions['property'] : null;
                $choices = isset($entityOptions['choices']) ? $entityOptions['choices'] : null;
                $preferredChoices = isset($entityOptions['preferred_choices']) ? $entityOptions['preferred_choices'] : [];

                if (!is_null($loader) && !($loader instanceof EntityLoaderInterface)) {
                    throw new InvalidOptionsException(sprintf(
                        'The sub option "loader" is expected to be of type "%s", but is of type "%s".',
                        implode('" or "', ['null', 'Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface']),
                        is_object($loader) ? get_class($loader) : gettype($loader)
                    ));
                }

                $normalizedOptions[$alias] = [
                    'class' => $class,
                    'query_builder' => $queryBuilder,
                    'label' => $label,
                    'property' => $property,
                    'loader' => $loader,
                    'choices' => $choices,
                    'preferred_choices' => $preferredChoices,
                ];
            }

            if (empty($normalizedOptions)) {
                throw new MissingOptionsException('The required option "options" is empty.');
            }

            return $normalizedOptions;
        };

        $placeholderNormalizer = function(Options $options, $placeholder) {
            if (false === $placeholder) {
                return;
            }

            return $placeholder;
        };

        $resolver->setDefaults([
            'em' => null,
            'options' => [],
            'choice_list' => $choiceList,
            'placeholder' => $placeholder,
            'data_class' => null,
        ]);

        $resolver->setNormalizers([
            'em' => $emNormalizer,
            'options' => $optionsNormalizers,
            'placeholder' => $placeholderNormalizer,
        ]);

        $resolver->setAllowedTypes([
            'em' => ['null', 'string', 'Doctrine\Common\Persistence\ObjectManager'],
            'options' => ['array'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_mixed_entity';
    }

    /**
     * @param ObjectManager $manager
     * @param $queryBuilder
     * @param $class
     * @return ORMQueryBuilderLoader
     */
    public function getLoader(ObjectManager $manager, $queryBuilder, $class)
    {
        return new ORMQueryBuilderLoader(
            $queryBuilder,
            $manager,
            $class
        );
    }
}