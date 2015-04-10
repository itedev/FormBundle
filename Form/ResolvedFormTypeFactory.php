<?php

namespace ITE\FormBundle\Form;

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
     * {@inheritdoc}
     */
    public function createResolvedType(FormTypeInterface $type, array $typeExtensions, ResolvedFormTypeInterface $parent = null)
    {
        return new ResolvedFormType($type, $typeExtensions, $parent);
    }
}