<?php

namespace ITE\FormBundle\Form;

use ITE\FormBundle\Proxy\ProxyFactory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\ResolvedFormTypeFactory as BaseResolvedFormTypeFactory;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * Class ResolvedFormTypeFactory
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ResolvedFormTypeFactory extends BaseResolvedFormTypeFactory
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @var ProxyFactory $proxyFactory
     */
    protected $proxyFactory;

    /**
     * @param ContainerInterface $container
     * @param ProxyFactory $proxyFactory
     */
    public function __construct(ContainerInterface $container, ProxyFactory $proxyFactory)
    {
        $this->container = $container;
        $this->proxyFactory = $proxyFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createResolvedType(
        FormTypeInterface $type,
        array $typeExtensions,
        ResolvedFormTypeInterface $parent = null
    ) {
        if ($type instanceof ContainerAwareInterface) {
            $type->setContainer($this->container);
        }

        return new ResolvedFormType($this->proxyFactory, $type, $typeExtensions, $parent);
    }
}
