<?php

namespace ITE\FormBundle\Service\Validator;

/**
 * Interface ValidatorInterface
 * @package ITE\FormBundle\Service\Validator
 */
interface ValidatorInterface
{
    /**
     * @param $form
     * @return array
     */
    public function getConstraints($form);
} 