<?php

namespace ITE\FormBundle\Form;

use ITE\FormBundle\Form\Builder\ButtonBuilder;
use ITE\FormBundle\Form\Builder\FormBuilder;
use ITE\FormBundle\Form\Builder\SubmitButtonBuilder;
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
     * @var ProxyFactory $proxyFactory
     */
    protected $proxyFactory;

    /**
     * @param ProxyFactory $proxyFactory
     * @param FormTypeInterface $innerType
     * @param array $typeExtensions
     * @param ResolvedFormTypeInterface|null $parent
     */
    public function __construct(
        ProxyFactory $proxyFactory,
        FormTypeInterface $innerType,
        array $typeExtensions = [],
        ResolvedFormTypeInterface $parent = null
    ) {
        parent::__construct($innerType, $typeExtensions, $parent);
        $this->proxyFactory = $proxyFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function newBuilder($name, $dataClass, FormFactoryInterface $factory, array $options)
    {
        if ($this->getInnerType() instanceof ButtonTypeInterface) {
            return new ButtonBuilder($name, $options);
        }

        if ($this->getInnerType() instanceof SubmitButtonTypeInterface) {
            return new SubmitButtonBuilder($name, $options);
        }

        return new FormBuilder($this->proxyFactory, $name, $dataClass, new EventDispatcher(), $factory, $options);
    }
}
