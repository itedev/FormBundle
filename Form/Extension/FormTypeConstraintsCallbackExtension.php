<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\Common\Util\ReflectionUtils;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;

/**
 * Class FormTypeConstraintsCallbackExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormTypeConstraintsCallbackExtension extends AbstractTypeExtension
{
    /**
     * @var MetadataFactoryInterface $metadataFactory
     */
    protected $metadataFactory;

    /**
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['constraints_callback'])) {
            return;
        }

        $constraintsCallback = $options['constraints_callback'];
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($constraintsCallback) {
            $form = $event->getForm();

            $constraints = call_user_func_array($constraintsCallback, [$form]);

            $config = $form->getConfig();
            $options = ReflectionUtils::getValue($config, 'options');
            $options['constraints'] = array_merge($options['constraints'], $constraints);
            ReflectionUtils::setValue($config, 'options', $options);
        }, 1);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setOptional([
            'constraints_callback',
        ]);
        $resolver->setAllowedTypes([
            'constraints_callback' => ['callable'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
