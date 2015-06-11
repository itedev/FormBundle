<?php

namespace ITE\FormBundle\Validation\Mapping\Factory;

use ITE\FormBundle\Validation\Mapping\FormMetadata;
use Symfony\Component\Form\FormInterface;

/**
 * Interface FormMetadataFactoryInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormMetadataFactoryInterface
{
    /**
     * @param FormInterface $form
     * @param bool $constraintConversion
     * @return FormMetadata
     */
    public function getMetadataFor(FormInterface $form, $constraintConversion = false);
}