<?php

namespace ITE\FormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BaseType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class MarkupType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MarkupType extends AbstractType
{
    /**
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'trim' => false,
            'required' => false,
            'read_only' => true,
            'mapped' => false,
            'error_bubbling' => false,
            'compound' => false,
            'auto_initialize' => false,
            'markup' => null,
        ]);
        $resolver->setAllowedValues([
            'mapped' => [false],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['markup'] = $this->getMarkup($form, $options);
    }

    /**
     * @param FormInterface $form
     * @param array $options
     * @return mixed|string
     */
    protected function getMarkup(FormInterface $form, array $options)
    {
        $parentForm = $form->getParent();
        $parentData = $parentForm->getData();

        if (is_callable($options['markup'])) {
            return call_user_func_array($options['markup'], [$parentForm]);
        } elseif (is_string($options['markup'])) {
            return $options['markup'];
        } else {
            $empty = null === $parentData || array() === $parentData;
            $propertyPath = $form->getPropertyPath();

            if (!$empty && null !== $propertyPath) {
                return $this->propertyAccessor->getValue($parentData, $propertyPath);
            }

            throw new \InvalidArgumentException('Invalid markup value');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_markup';
    }
}