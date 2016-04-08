<?php

namespace ITE\FormBundle\Form;

use ITE\FormBundle\Proxy\ProxyFactory;
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
     * @var ProxyFactory $proxyFactory
     */
    protected $proxyFactory;

    /**
     * @param ProxyFactory $proxyFactory
     */
    public function __construct(ProxyFactory $proxyFactory)
    {
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
        return new ResolvedFormType($this->proxyFactory, $type, $typeExtensions, $parent);
    }
}
