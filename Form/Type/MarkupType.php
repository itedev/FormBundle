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
 * @package ITE\FormBundle\Form\Type
 */
class MarkupType extends AbstractType
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function __construct(PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'trim' => false,
            'required' => true,
            'read_only' => true,
            'mapped' => false,
            'error_bubbling' => false,
            'compound' => false,
            'auto_initialize' => false,
            'markup' => null,
            'markup_builder' => null,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $data = array_key_exists('data', $options)
            ? $options['data']
            : $this->propertyAccessor->getValue($form->getParent()->getConfig()->getData(), $form->getPropertyPath());

        $view->vars = array_replace($view->vars, array(
            'data' => $data,
            'value' => $data,
        ));
        $view->vars['markup'] = $this->getMarkup($data, $options);
    }

    /**
     * @param $data
     * @param array $options
     * @return mixed|string
     */
    protected function getMarkup($data, array $options)
    {
        if (is_callable($options['markup_builder'])) {
            return call_user_func_array($options['markup_builder'], [$data]);
        } elseif (isset($options['markup'])) {
            return $options['markup'];
        } elseif (is_object($data) && method_exists($data, '__toString')) {
            return (string) $data;
        }

        return '';
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