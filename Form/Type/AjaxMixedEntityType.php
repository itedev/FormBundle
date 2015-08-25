<?php

namespace ITE\FormBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use ITE\FormBundle\Form\ChoiceList\AjaxMixedEntityChoiceList;
use ITE\FormBundle\Form\ChoiceList\MixedEntityChoiceList;
use ITE\FormBundle\Form\ChoiceList\AjaxEntityChoiceList;
use ITE\FormBundle\Form\DataTransformer\MixedEntityToIdTransformer;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Component\Form\Exception\RuntimeException;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\ChoicesToValuesTransformer;
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
use Symfony\Component\Routing\RouterInterface;

/**
 * Class MixedEntityType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxMixedEntityType extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param ManagerRegistry $registry
     * @param RouterInterface $router
     * @param PropertyAccessorInterface|null $propertyAccessor
     */
    public function __construct(ManagerRegistry $registry, RouterInterface $router, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->registry = $registry;
        $this->router = $router;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ('choice' === $options['widget']) {
            // choice
            if ($options['multiple']) {
                $builder->addViewTransformer(new ChoicesToValuesTransformer($options['choice_list']));
            } else {
                $builder->addViewTransformer(new ChoiceToValueTransformer($options['choice_list']));
            }
        } else {
            // hidden
            $builder->addViewTransformer(new MixedEntityToIdTransformer(
                $options['options'],
                $options['multiple'],
                $options['separator']
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ('choice' === $options['widget']) {
            // choice

            $data = $form->getData();
            $empty = null === $data || [] === $data;
            if (!$empty) {
                $options['choice_list']->setData($data);
            }

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
                'preferred_choices' => [],
                'choices' => $options['choice_list']->getRemainingViews(),
                'separator' => '-------------------',
                'placeholder' => null,
            ));

            if ($options['multiple']) {
                $view->vars['is_selected'] = function ($choice, array $values) {
                    return in_array($choice, $values, true);
                };
            } else {
                $view->vars['is_selected'] = function ($choice, $value) {
                    return $choice === $value;
                };
            }

            // Check if the choices already contain the empty value
            $view->vars['placeholder_in_choices'] = 0 !== count($options['choice_list']->getChoicesForValues(['']));

            // Only add the empty value option if this is not the case
            if (null !== $options['placeholder'] && !$view->vars['placeholder_in_choices']) {
                $view->vars['placeholder'] = $options['placeholder'];
            }

            if ($options['multiple']) {
                $view->vars['full_name'] .= '[]';
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $registry = $this->registry;
        $router = $this->router;
        $propertyAccessor = $this->propertyAccessor;
        $self = $this;

        $choiceList = function(Options $options) use ($propertyAccessor) {
            $entitiesOptions = $options['options'];

            $entityChoicesLists = [];
            $entityLabels = [];
            foreach ($entitiesOptions as $alias => $entityOptions) {
                $em = $entityOptions['em'];
                $class = $entityOptions['class'];
                $loader = $entityOptions['loader'];
                $property = $entityOptions['property'];
                $label = $entityOptions['label'];

                $entityChoicesLists[$alias] = new AjaxEntityChoiceList(
                    $em,
                    $class,
                    $property,
                    $loader,
                    $propertyAccessor
                );
                $entityLabels[$alias] = $label;
            }

            return new AjaxMixedEntityChoiceList(
                $entitiesOptions,
                $entityChoicesLists,
                $entityLabels
            );
        };

        $placeholder = function(Options $options) {
            return $options['required'] ? null : '';
        };

        $optionsNormalizers = function(Options $options, $entitiesOptions) use ($registry, $self) {
            $normalizedOptions = [];
            foreach ($entitiesOptions as $alias => $entityOptions) {
                if (!isset($entityOptions['class'])) {
                    throw new MissingOptionsException('The required sub option "class" is missing.');
                }

                $em = isset($entityOptions['em']) ? $entityOptions['em'] : null;
                $class = $entityOptions['class'];
                $queryBuilder = isset($entityOptions['query_builder']) ? $entityOptions['query_builder'] : null;
                $loader = isset($entityOptions['loader']) ? $entityOptions['loader'] : null;
                $label = isset($entityOptions['label']) ? $entityOptions['label'] : null;
                $property = isset($entityOptions['property']) ? $entityOptions['property'] : null;
                $choices = isset($entityOptions['choices']) ? $entityOptions['choices'] : null;
                $preferredChoices = isset($entityOptions['preferred_choices']) ? $entityOptions['preferred_choices'] : [];

                if (!is_null($em) && !is_string($em) && !($em instanceof ObjectManager)) {
                    throw new InvalidOptionsException(sprintf(
                        'The sub option "em" is expected to be of type "%s", but is of type "%s".',
                        implode('" or "', ['null', 'string', 'Doctrine\Common\Persistence\ObjectManager']),
                        is_object($em) ? get_class($em) : gettype($em)
                    ));
                }
                if (null !== $em) {
                    if (!($em instanceof ObjectManager)) {
                        $em = $registry->getManager($em);
                    }
                } else {
                    $em = $registry->getManagerForClass($class);
                    if (null === $em) {
                        throw new RuntimeException(sprintf(
                            'Class "%s" seems not to be a managed Doctrine entity. '.
                            'Did you forget to map it?',
                            $class
                        ));
                    }
                }

                if (!is_null($loader) && !($loader instanceof EntityLoaderInterface)) {
                    throw new InvalidOptionsException(sprintf(
                        'The sub option "loader" is expected to be of type "%s", but is of type "%s".',
                        implode('" or "', ['null', 'Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface']),
                        is_object($loader) ? get_class($loader) : gettype($loader)
                    ));
                }
                $qb = isset($queryBuilder)
                    ? $queryBuilder
                    : $em->getRepository($class)->createQueryBuilder('e');

                $defaultLoader = $self->getLoader($em, $qb, $class);
                $loader = isset($loader) ? $loader : $defaultLoader;

                $normalizedOptions[$alias] = [
                    'em' => $em,
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

        $urlNormalizer = function(Options $options, $url) use ($router) {
            if (!empty($options['route'])) {
                return $router->generate($options['route'], $options['route_parameters']);
            } elseif (!empty($url)) {
                return $url;
            } else {
                throw new \RuntimeException('You must specify "route" or "url" option.');
            }
        };

        $resolver->setDefaults([
            'options' => [],
            'choice_list' => $choiceList,
            'placeholder' => $placeholder,
            'data_class' => null,
            'route' => null,
            'route_parameters' => [],
            'url' => null,
            'multiple' => false,
            'widget' => 'choice',
            'separator' => ',',
            'compound' => false,
        ]);
        $resolver->setNormalizers([
            'options' => $optionsNormalizers,
            'placeholder' => $placeholderNormalizer,
            'url' => $urlNormalizer,
        ]);
        $resolver->setAllowedTypes([
            'options' => ['array'],
            'multiple' => ['bool'],
        ]);
        $resolver->setAllowedValues([
            'widget' => [
                'choice',
                'hidden',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_ajax_mixed_entity';
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