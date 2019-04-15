<?php

namespace ITE\FormBundle\Form\Type\Hidden;

use ITE\FormBundle\Form\DataTransformer\MixedToSerializedTransformer;
use JMS\Serializer\Exclusion\GroupsExclusionStrategy;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MixedHiddenType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MixedHiddenType extends AbstractType
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * MixedHiddenType constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new MixedToSerializedTransformer(
            $this->serializer,
            $options['class'],
            $options['serialization_format'],
            $options['serialization_groups']
        ));
    }

    /**
     * @inheritDoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired([
            'class',
        ]);
        $resolver->setDefaults([
            'serialization_format' => 'json',
            'serialization_groups' => [GroupsExclusionStrategy::DEFAULT_GROUP],
        ]);
        $resolver->setAllowedTypes([
            'class' => ['string'],
            'serialization_format' => ['string'],
            'serialization_groups' => ['array'],
        ]);
        $resolver->setAllowedValues([
            'serialization_format' => ['xml', 'json', 'yml'],
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
        return 'ite_mixed_hidden';
    }
}
