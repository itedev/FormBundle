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
            'markup_raw' => true,
            'translate' => true,
        ]);
        $resolver->setAllowedValues([
            'mapped' => [false],
        ]);
        $resolver->setAllowedTypes([
            'translate' => 'bool',
            'markup_raw' => 'bool',
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
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $markup = $this->getMarkup($form, $options);
        $view->vars['value'] = $markup;
        $view->vars['data'] = $markup;
        $view->vars['translate'] = $options['translate'];

        if ($this->formatter) {
            if (null !== $options['formatter']) {
                if (true === $options['formatter']) {
                    $parentForm = $form->getParent();
                    $parentData = $parentForm->getData();
                    $propertyPath = $form->getPropertyPath();

                    $empty = null === $parentData || [] === $parentData;

                    if (!$empty && null !== $propertyPath) {
                        $markup = $this->formatter->formatProperty($parentData, (string) $propertyPath, $options['formatter_options']);
                    } else {
                        $markup = null;
                    }
                } else {
                    $markup = $this->formatter->format($markup, $options['formatter'], $options['formatter_options']);
                }
            }
        }

        $view->vars['markup'] = $markup;
        $view->vars['markup_raw'] = $options['markup_raw'];
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
