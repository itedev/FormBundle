<?php

namespace ITE\FormBundle\Form\Type;

use ITE\FormatterBundle\Formatter\FormatterManagerInterface;
use Symfony\Component\Form\AbstractType;
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
     * @var FormatterManagerInterface|null $formatter
     */
    protected $formatter;

    /**
     * @var PropertyAccessorInterface
     */
    protected $propertyAccessor;

    /**
     * @param FormatterManagerInterface|null $formatter
     */
    public function __construct(FormatterManagerInterface $formatter = null)
    {
        $this->formatter = $formatter;
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

        if ($this->formatter) {
            $resolver->setDefaults([
                'formatter' => null,
                'formatter_options' => [],
            ]);
            $resolver->setAllowedTypes([
                'formatter_options' => ['array'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $markup = $this->getMarkup($form, $options);
        $view->vars['value'] = $markup;
        $view->vars['data'] = $markup;

        if ($this->formatter) {
            if (null !== $options['formatter']) {
                $markup = $this->formatter->format($markup, $options['formatter'], $options['formatter_options']);
            }
        }

        $view->vars['markup'] = $markup;
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
        } elseif (is_scalar($options['markup'])) {
            return $options['markup'];
        } else {
            $empty = null === $parentData || [] === $parentData;
            $propertyPath = $form->getPropertyPath();

            if (!$empty && null !== $propertyPath) {
                return $this->propertyAccessor->getValue($parentData, $propertyPath);
            }

            return null;
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
