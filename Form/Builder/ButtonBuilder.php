<?php

namespace ITE\FormBundle\Form\Builder;

use Symfony\Component\Form\ButtonBuilder as BaseButtonBuilder;

/**
 * Class ButtonBuilder
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ButtonBuilder extends BaseButtonBuilder
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
