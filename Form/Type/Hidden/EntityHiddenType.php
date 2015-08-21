<?php

namespace ITE\FormBundle\Form\Type\Hidden;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use ITE\FormBundle\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class IntegerHiddenType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class EntityHiddenType extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new EntityToIdTransformer(
            $options['em'],
            $options['class'],
            $options['loader'],
            $options['multiple'],
            $options['separator']
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $registry = $this->registry;
        $type = $this;

        $loader = function(Options $options) use ($type) {
            $queryBuilder = (null !== $options['query_builder'])
                ? $options['query_builder']
                : $options['em']->getRepository($options['class'])->createQueryBuilder('e');

            return $type->getLoader($options['em'], $queryBuilder, $options['class']);
        };

        $emNormalizer = function(Options $options, $em) use ($registry) {
            /* @var ManagerRegistry $registry */
            if (null !== $em) {
                if ($em instanceof ObjectManager) {
                    return $em;
                }

                return $registry->getManager($em);
            }

            $em = $registry->getManagerForClass($options['class']);

            if (null === $em) {
                throw new RuntimeException(sprintf(
                    'Class "%s" seems not to be a managed Doctrine entity. '.
                    'Did you forget to map it?',
                    $options['class']
                ));
            }

            return $em;
        };

        $resolver->setDefaults([
            'em' => null,
            'query_builder' => null,
            'loader' => $loader,
            'multiple' => false,
            'separator' => ',',
        ]);
        $resolver->setRequired([
            'class',
        ]);
        $resolver->setNormalizers([
            'em' => $emNormalizer,
        ]);
        $resolver->setAllowedTypes([
            'em' => ['null', 'string', 'Doctrine\Common\Persistence\ObjectManager'],
            'loader' => ['null', 'Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface'],
            'multiple' => ['bool'],
            'separator' => ['string'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_entity_hidden';
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