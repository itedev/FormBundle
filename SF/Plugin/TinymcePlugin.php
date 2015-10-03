<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\FormBundle\SF\AbstractPlugin;

/**
 * Class TinymcePlugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class TinymcePlugin extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'tinymce';
    }
}