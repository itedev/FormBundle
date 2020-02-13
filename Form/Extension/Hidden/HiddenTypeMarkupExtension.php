<?php

namespace ITE\FormBundle\Form\Extension\Hidden;

use ITE\FormatterBundle\Formatter\FormatterManagerInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
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
            'markupable' => false,
            'translate' => true,
        ]);
        $resolver->setOptional([
            'markup',
            'markup_attr',
            'markup_property_path',
            'markup_strict',
        ]);
        $resolver->setAllowedTypes([
            'markup_attr' => ['array'],
            'markup_property_path' => ['string'],
            'translate' => ['bool'],
            'markup_strict' => ['bool'],
        ]);

        if ($this->formatter) {
            $resolver->setOptional([
                'formatter',
                'formatter_options',
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
        if (!$options['markupable']) {
            return;
        }

        $markup = $this->getMarkup($form, $options);
        if ($this->formatter && isset($options['formatter']) && null !== $options['formatter']) {
            $formatterOptions = isset($options['formatter_options']) ? $options['formatter_options'] : [];
            $markup = $this->formatter->format($markup, $options['formatter'], $formatterOptions);
        }

        $view->vars['translate'] = $options['translate'];
        $view->vars['markup'] = $markup;
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

        if (isset($options['markup'])) {
            if (is_callable($options['markup'])) {
                return call_user_func_array($options['markup'], [$parentForm, $parentData]);
            } elseif (is_scalar($options['markup'])) {
                return $options['markup'];
            } else {
                return null;
            }
        } else {
            $empty = null === $parentData || [] === $parentData;
            $propertyPath = isset($options['markup_property_path'])
                ? $options['markup_property_path']
                : $form->getPropertyPath();
            $strict = $options['markup_strict'] ?? true;

            if (!$empty && null !== $propertyPath) {
                try {
                    $markup = $this->propertyAccessor->getValue($parentData, $propertyPath);
                } catch (\Exception $e) {
                    if ($strict) {
                        throw $e;
                    }
                    $markup = null;
                }

                return $markup;
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
