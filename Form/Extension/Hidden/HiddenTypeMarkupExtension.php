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
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getExtendedType(), $view->vars['block_prefixes']) + 1,
            0,
            'ite_hidden_markup'
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
    public function getExtendedType()
    {
        return 'hidden';
    }

}