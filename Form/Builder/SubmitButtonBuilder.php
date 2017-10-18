<?php

namespace ITE\FormBundle\Form\Builder;

use Symfony\Component\Form\SubmitButtonBuilder as BaseSubmitButtonBuilder;

/**
 * Class SubmitButtonBuilder
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class SubmitButtonBuilder extends BaseSubmitButtonBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getOriginalType()
    {
        return $this->getOption('original_type');
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalOptions()
    {
        return $this->getOption('original_options');
    }
}
