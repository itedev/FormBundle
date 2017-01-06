<?php

namespace ITE\FormBundle\Form\Extension\Hidden;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class HiddenTypeMarkupExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class HiddenTypeMarkupExtension extends AbstractTypeExtension
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
        $resolver->setOptional([
            'markup',
            'markup_attr',
            'markup_property_path',
        ]);
        $resolver->setAllowedTypes([
            'markup_attr' => ['array'],
            'markup_property_path' => ['string'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!isset($options['markup'])) {
            return;
        }

        $view->vars['markup'] = $this->getMarkup($form, $options);
        $view->vars['markup_attr'] = isset($options['markup_attr']) ? $options['markup_attr'] : [];

        $typeName = $form->getConfig()->getType()->getName();
        $offset = array_search($this->getExtendedType(), $view->vars['block_prefixes']);
        $limit = array_search($typeName, $view->vars['block_prefixes']) - $offset + 1;

        array_splice(
            $view->vars['block_prefixes'],
            $offset,
            $limit,
            'ite_markup_hidden'
        );
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
            $empty = null === $parentData || [] === $parentData;
            $propertyPath = isset($options['markup_property_path'])
                ? $options['markup_property_path']
                : $form->getPropertyPath();

            if (!$empty && null !== $propertyPath) {
                return $this->propertyAccessor->getValue($parentData, $propertyPath);
            }

            return null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'hidden';
    }
}
