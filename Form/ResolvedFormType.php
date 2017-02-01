<?php

namespace ITE\FormBundle\Form;

use ITE\FormBundle\Form\Builder\ButtonBuilder;
use ITE\FormBundle\Form\Builder\FormBuilder;
use ITE\FormBundle\Form\Builder\SubmitButtonBuilder;
use ITE\FormBundle\OptionsResolver\MultidimensionalOptionsResolver;
use ITE\FormBundle\Proxy\ProxyFactory;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\ButtonTypeInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormType as BaseResolvedFormType;
use Symfony\Component\Form\ResolvedFormTypeInterface;
use Symfony\Component\Form\SubmitButtonTypeInterface;

/**
 * Class ResolvedFormType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ResolvedFormType extends BaseResolvedFormType
{
    /**
     * @var MultidimensionalOptionsResolver
     */
    private $optionsResolver;

    /**
     * @var ProxyFactory $proxyFactory
     */
    protected $proxyFactory;

    /**
     * @var array $classes
     */
    protected $classes;

    /**
     * @param ProxyFactory $proxyFactory
     * @param array $classes
     * @param FormTypeInterface $innerType
     * @param array $typeExtensions
     * @param ResolvedFormTypeInterface|null $parent
     */
    public function __construct(
        ProxyFactory $proxyFactory,
        array $classes,
        FormTypeInterface $innerType,
        array $typeExtensions = [],
        ResolvedFormTypeInterface $parent = null
    ) {
        parent::__construct($innerType, $typeExtensions, $parent);
        $this->proxyFactory = $proxyFactory;
        $this->classes = $classes;
    }

    /**
     * @return MultidimensionalOptionsResolver
     */
    public function getOptionsResolver()
    {
        if (null === $this->optionsResolver) {
            if (null !== $this->getParent()) {
                $this->optionsResolver = clone $this->getParent()->getOptionsResolver();
            } else {
                $this->optionsResolver = new MultidimensionalOptionsResolver();
            }

            $this->getInnerType()->setDefaultOptions($this->optionsResolver);

            foreach ($this->getTypeExtensions() as $extension) {
                $extension->setDefaultOptions($this->optionsResolver);
            }
        }

        return $this->optionsResolver;
    }

    /**
     * {@inheritdoc}
     */
    protected function newBuilder($name, $dataClass, FormFactoryInterface $factory, array $options)
    {
        if ($this->getInnerType() instanceof ButtonTypeInterface) {
            return new $this->classes['button_builder']($name, $options);
        }

        if ($this->getInnerType() instanceof SubmitButtonTypeInterface) {
            return new $this->classes['submit_button_builder']($name, $options);
        }

        return new $this->classes['form_builder'](
            $this->proxyFactory,
            $this->classes['form'],
            $name,
            $dataClass,
            new EventDispatcher(),
            $factory,
            $options
        );
    }
}
