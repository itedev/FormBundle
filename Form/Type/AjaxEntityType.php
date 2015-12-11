<?php

namespace ITE\FormBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use ITE\FormBundle\Form\ChoiceList\AjaxEntityChoiceList;
use ITE\FormBundle\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AjaxEntityType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxEntityType extends AbstractType
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
     * @param ManagerRegistry $registry
     * @param PropertyAccessorInterface|null $propertyAccessor
     */
    public function __construct(ManagerRegistry $registry, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ('hidden' === $options['widget']) {
            $builder->addViewTransformer(new EntityToIdTransformer(
                $options['em'],
                $options['class'],
                $options['loader'],
                $options['multiple'],
                $options['separator']
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $registry = $this->registry;
        $propertyAccessor = $this->propertyAccessor;
        $self = $this;

        $loader = function (Options $options) use ($self) {
            $queryBuilder = (null !== $options['query_builder'])
                ? $options['query_builder']
                : $options['em']->getRepository($options['class'])->createQueryBuilder('e');

            return $self->getLoader($options['em'], $queryBuilder, $options['class']);
        };

        $choiceList = function (Options $options) use ($propertyAccessor) {
            return new AjaxEntityChoiceList(
                $options['em'],
                $options['class'],
                $options['property'],
                $options['loader'],
                $propertyAccessor
            );
        };

        $resolver->setDefaults([
            'choice_list' => $choiceList,
        ]);

        $emNormalizer = function (Options $options, $em) use ($registry) {
            /* @var ManagerRegistry $registry */
            if (null !== $em) {
                if ($em instanceof ObjectManager) {
                    return $em;
                }

                return $registry->getManager($em);
            }

            $em = $registry->getManagerForClass($options['class']);
            if (null === $em) {
                throw new \RuntimeException(sprintf(
                    'Class "%s" seems not to be a managed Doctrine entity. '.
                    'Did you forget to map it?',
                    $options['class']
                ));
            }

            return $em;
        };

        $resolver->setDefaults([
            'em' => null,
            'property' => null,
            'query_builder' => null,
            'loader' => $loader,
            'choice_list' => $choiceList,
        ]);
        $resolver->setRequired(['class']);
        $resolver->setNormalizers([
            'em' => $emNormalizer,
        ]);
        $resolver->setAllowedTypes([
            'em' => ['null', 'string', 'Doctrine\Common\Persistence\ObjectManager'],
            'loader' => ['null', 'Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_ajax_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_ajax_entity';
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
