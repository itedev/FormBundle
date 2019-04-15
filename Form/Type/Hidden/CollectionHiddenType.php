<?php

namespace ITE\FormBundle\Form\Type\Hidden;

use ITE\FormBundle\Form\DataTransformer\CollectionToSerializedTransformer;
use ITE\FormBundle\Form\EventListener\ResizeSerializedCollectionSubscriber;
use ITE\FormBundle\Form\Model\FormMappingItem;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class CollectionHiddenType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CollectionHiddenType extends AbstractType
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * MixedHiddenType constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new ResizeSerializedCollectionSubscriber(
                $this->propertyAccessor,
                $options['mapping']
            ))
            ->addViewTransformer(new CollectionToSerializedTransformer(
                $this->serializer,
                $options['class'],
                $options['serialization_format'],
                $options['serialization_groups']
            ))
        ;
    }

    /**
     * @inheritDoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $mappingNormalizer = function (Options $options, $mapping) {
            if (is_array($mapping)) {
                $mapping = FormMappingItem::createFromArray($mapping);
            }

            return $mapping;
        };

        $resolver->setRequired([
            'class',
            'mapping',
        ]);
        $resolver->setDefaults([
//            'error_bubbling' => false,
            'serialization_format' => 'json',
            'serialization_groups' => [GroupsExclusionStrategy::DEFAULT_GROUP],
        ]);
        $resolver->setAllowedTypes([
            'class' => ['string'],
            'mapping' => ['array', 'ITE\FormBundle\Form\Model\FormMappingItem'],
            'serialization_format' => ['string'],
            'serialization_groups' => ['array'],
        ]);
        $resolver->setAllowedValues([
            'serialization_format' => ['xml', 'json', 'yml'],
        ]);
        $resolver->setNormalizers([
            'mapping' => $mappingNormalizer,
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
        return 'ite_collection_hidden';
    }
}
