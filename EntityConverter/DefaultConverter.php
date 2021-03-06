<?php

namespace ITE\FormBundle\EntityConverter;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use ITE\FormBundle\Util\MixedEntityUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ExpressionLanguageProvider;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Form\Exception\StringCastException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Class EntityConverter
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DefaultConverter implements ConverterInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Request $requestStack
     */
    protected $requestStack;

    /**
     * @var PropertyAccessor
     */
    protected $propertyAccessor;

    /**
     * @var ExpressionLanguage|null
     */
    protected $expressionLanguage;

    /**
     * @param ContainerInterface $container
     * @param EntityManager $em
     * @param RequestStack $requestStack
     */
    public function __construct(ContainerInterface $container, EntityManager $em, RequestStack $requestStack)
    {
        $this->container = $container;
        $this->em = $em;
        $this->requestStack = $requestStack;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @return ExpressionLanguage|null
     */
    private function getExpressionLanguage()
    {
        if (null === $this->expressionLanguage) {
            $this->expressionLanguage = new ExpressionLanguage();
            $this->expressionLanguage->registerProvider(new ExpressionLanguageProvider());
        }

        return $this->expressionLanguage;
    }

    /**
     * @todo: refactor!
     *
     * @param array $entities
     * @param array $options
     * @return array
     */
    public function convert($entities, array $options = [])
    {
        if (null === $entities) {
            return;
        }

        if (!is_array($entities) && !($entities instanceof \Traversable)) {
            if (true === $options['multiple']) {
                throw new \InvalidArgumentException('You must pass "array" or instance of "Traversable"');
            }

            $entities = [$entities];
        }

        $mixed = array_key_exists('options', $options);

        $config = [];
        $aliases = [];
        if (!$mixed) {
            $class = isset($options['class']) ? $options['class'] : null;
            if (null === $class) {
                foreach ($entities as $entity) {
                    if (is_array($entity)) {
                        foreach ($entity as $item) {
                            $class = get_class($item);

                            break 2;
                        }
                    } else {
                        $class = get_class($entity);

                        break;
                    }
                }
            }

            if (null === $class) {
                return [];
            }

            $classMetadata = $this->em->getClassMetadata($class);
            $class = $classMetadata->getName();
            $labelPath = $options['property'] ?? $this->getLabelPathFromRequest();
            $labelExpression = $options['property_expression'] ?? null;

            $config[0] = [
                'class' => $class,
                'classMetadata' => $classMetadata,
                'labelPath' => $labelPath,
                'labelExpression' => $labelExpression,
            ];
            $aliases[$class] = 0;
        } else {
            foreach ($options['options'] as $alias => $entityOptions) {
                $class = $entityOptions['class'];
                $classMetadata = $this->em->getClassMetadata($class);
                $class = $classMetadata->getName();
                $labelPath = $entityOptions['property'] ?? null;
                $labelExpression = $entityOptions['property_expression'] ?? null;

                $config[$alias] = [
                    'class' => $class,
                    'classMetadata' => $classMetadata,
                    'labelPath' => $labelPath,
                    'labelExpression' => $labelExpression,
                ];
                $aliases[$class] = $alias;
            }
        }

        $choices = [];
        $entityOptionsCallbackArguments = $this->getEntityOptionsCallbackArguments($options['entity_options_callback_arguments']);
        foreach ($entities as $i => $entity) {
            if (is_array($entity)) {
                if (!empty($entity) && !isset($choices[$i])) {
                    $choices[$i] = [];
                }
                foreach ($entity as $item) {
                    $alias = $this->getAlias($item, $aliases);
                    $value = $this->getValue($item, $mixed, $config, $alias);
                    $label = $this->getLabel($item, $config, $alias);

                    if (null !== $options['entity_options_callback']) {
                        $choices[$i][] = [
                            'value'   => $value,
                            'label'   => $label,
                            'options' => (array) call_user_func_array($options['entity_options_callback'], array_merge(
                                [$entity],
                                $entityOptionsCallbackArguments
                            )),
                        ];
                    } else {
                        $choices[$i][] = [
                            'value' => $value,
                            'label' => $label,
                        ];
                    }
                }
            } else {
                $alias = $this->getAlias($entity, $aliases);
                $value = $this->getValue($entity, $mixed, $config, $alias);
                $label = $this->getLabel($entity, $config, $alias);

                if (null !== $options['entity_options_callback']) {
                    $choices[] = [
                        'value'   => $value,
                        'label'   => $label,
                        'options' => (array) call_user_func_array($options['entity_options_callback'], array_merge(
                            [$entity],
                            $entityOptionsCallbackArguments
                        )),
                    ];
                } else {
                    $choices[] = [
                        'value' => $value,
                        'label' => $label,
                    ];
                }
            }
        }

        return $options['multiple'] ? $choices : array_pop($choices);
    }

    /**
     * @param array $arguments
     * @return array
     */
    protected function getEntityOptionsCallbackArguments(array $arguments)
    {
        foreach ($arguments as $i => $argument) {
            $argument = $this->getExpressionLanguage()->evaluate($argument, [
                'container' => $this->container,
            ]);
            $arguments[$i] = $argument;
        }

        return $arguments;
    }

    /**
     * @param $entity
     * @param $aliases
     * @return mixed
     */
    protected function getAlias($entity, $aliases)
    {
        $class = ClassUtils::getRealClass(get_class($entity));
        if (!isset($aliases[$class])) {
            $classMetadata = $this->em->getClassMetadata(get_class($entity));

            $class = $classMetadata->rootEntityName;
        }

        $alias = $aliases[$class];

        return $alias;
    }

    /**
     * @param $entity
     * @param $mixed
     * @param $config
     * @param $alias
     * @return string
     */
    protected function getValue($entity, $mixed, $config, $alias)
    {
        // @todo: check it
        $classMetadata = $config[$alias]['classMetadata'];
        if (!$classMetadata) {
            return '';
        }

        $value = (string) current($classMetadata->getIdentifierValues($entity));

        return $mixed ? MixedEntityUtils::wrapValue($value, $alias) : $value;
    }

    /**
     * @return string|null
     */
    protected function getLabelPathFromRequest()
    {
        $request = $this->requestStack->getMasterRequest();

        $property = $request->query->get('property');
        $property = !empty($property) ? $property : null;

        return $property;
    }

    /**
     * @param $entity
     * @param $config
     * @param $alias
     * @return null|string
     */
    protected function getLabel($entity, $config, $alias)
    {
        $labelPath = $config[$alias]['labelPath'];
        $labelExpression = $config[$alias]['labelExpression'];

        $label = null;
        if ($labelPath) {
            $label = (string) $this->propertyAccessor->getValue($entity, $labelPath);
        } elseif ($labelExpression) {
            $label = $this->getExpressionLanguage()->evaluate($labelExpression, [
               'container' => $this->container,
               'entity' => $entity,
            ]);
        } elseif (is_object($entity) && method_exists($entity, '__toString')) {
            $label = (string) $entity;
        } else {
            throw new StringCastException(sprintf('A "__toString()" method was not found on the objects of type "%s" passed to the choice field. To read a custom getter instead, set the argument $labelPath to the desired property path.', get_class($entity)));
        }

        return $label;
    }

//    /**
//     * @param mixed $files
//     *
//     * @return \Traversable
//     */
//    private function toIterator($files)
//    {
//        if (!$files instanceof \Traversable) {
//            $files = new \ArrayObject(is_array($files) ? $files : array($files));
//        }
//
//        return $files;
//    }
}
